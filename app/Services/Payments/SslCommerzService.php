<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\PaymentChannel;
use App\Models\PaymentOnlineTransaction as Txn;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * SSLCommerz hosted-checkout integration.
 *
 * Flow: initiate() → parent pays on the gateway page → gateway POSTs to success/fail/cancel
 * (browser) and IPN (server). A payment is recorded ONLY after the validation API confirms
 * it and the amount/currency match (settle()). settle() is idempotent: success page and IPN
 * may both arrive, and a row lock ensures the payment is recorded once.
 */
class SslCommerzService
{
    /** Mobile-banking / card shortcuts shown to parents → SSLCommerz multi_card_name. */
    public const METHODS = [
        'mobile' => 'mobilebank',
        'card'   => 'visacard,mastercard,amexcard',
        'all'    => null,
    ];

    public function __construct(private InvoiceService $invoices)
    {
    }

    public function activeChannel(): ?PaymentChannel
    {
        return PaymentChannel::active()->orderBy('id')->first();
    }

    public function isEnabled(): bool
    {
        return $this->activeChannel() !== null;
    }

    /**
     * Amount the parent can pay online now (the full balance), or null if not payable.
     */
    public function payableAmount(Invoice $invoice): ?float
    {
        $balance = round($invoice->balance_due, 2);

        return $invoice->is_open && $balance >= config('payments.min_amount') ? $balance : null;
    }

    /**
     * Create a gateway session and return the URL to redirect the parent to.
     *
     * @throws RuntimeException with a parent-friendly message
     */
    public function initiate(Invoice $invoice, string $method = 'all'): string
    {
        $channel = $this->activeChannel();
        if (!$channel) {
            throw new RuntimeException('Online payment is not available right now. Please pay at the front desk.');
        }

        $invoice->loadMissing(['parent.user', 'child', 'payments']);
        $amount = $this->payableAmount($invoice);
        if ($amount === null) {
            throw new RuntimeException($invoice->is_open
                ? 'The amount due is below the online payment minimum of ৳' . config('payments.min_amount') . '. Please pay at the front desk.'
                : 'This invoice is not open for payment.');
        }

        $txn = Txn::create([
            'payment_channel_id'  => $channel->id,
            'transactionable_type' => Invoice::class,
            'transactionable_id'  => $invoice->id,
            'transaction_id'      => $this->newTransactionId(),
            'invoice_id'          => $invoice->id,
            'amount'              => $amount,
            'is_overdue'          => $invoice->status === 'overdue' || $invoice->due_date?->isPast(),
            'status'              => Txn::STATUS_INITIATED,
        ]);

        $parent = $invoice->parent;
        $payload = [
            'store_id'         => $channel->store_id,
            'store_passwd'     => $channel->store_password,
            'total_amount'     => number_format($amount, 2, '.', ''),
            'currency'         => config('payments.currency'),
            'tran_id'          => $txn->transaction_id,
            'success_url'      => $this->callbackUrl('success'),
            'fail_url'         => $this->callbackUrl('fail'),
            'cancel_url'       => $this->callbackUrl('cancel'),
            'ipn_url'          => $this->callbackUrl('ipn'),
            'cus_name'         => $parent?->full_name ?: 'Parent',
            'cus_email'        => $parent?->user?->email ?: (config('parentapp.organization.email') ?: 'noreply@example.com'),
            'cus_add1'         => $parent?->address ?: 'N/A',
            'cus_city'         => $parent?->city ?: 'Dhaka',
            'cus_country'      => 'Bangladesh',
            'cus_phone'        => $parent?->mobile ?: '01700000000',
            'shipping_method'  => 'NO',
            'num_of_item'      => 1,
            'product_name'     => Str::limit('Invoice ' . $invoice->invoice_number . ' - ' . ($invoice->child?->full_name ?? ''), 250, ''),
            'product_category' => 'Daycare fees',
            'product_profile'  => 'non-physical-goods',
            'value_a'          => (string) $invoice->id,
            'value_b'          => $invoice->invoice_number,
        ];
        if ($cards = self::METHODS[$method] ?? null) {
            $payload['multi_card_name'] = $cards;
        }

        $txn->update(['initiate_request' => json_encode(array_merge($payload, ['store_passwd' => '***']))]);

        try {
            $response = $this->http()->asForm()->post($channel->endpoint('initiate_endpoint'), $payload);
            $body = $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz initiate failed', ['tran_id' => $txn->transaction_id, 'error' => $e->getMessage()]);
            $txn->update(['status' => Txn::STATUS_FAILED, 'remarks' => 'Initiate error: ' . Str::limit($e->getMessage(), 250)]);
            throw new RuntimeException('Could not reach the payment gateway. Please try again in a moment.');
        }

        $txn->update(['initiate_response' => Str::limit($response->body(), 60000, '')]);

        if (($body['status'] ?? null) !== 'SUCCESS' || empty($body['GatewayPageURL'])) {
            $txn->update([
                'status'  => Txn::STATUS_FAILED,
                'remarks' => 'Initiate rejected: ' . Str::limit((string) ($body['failedreason'] ?? 'unknown'), 250),
            ]);
            throw new RuntimeException('The payment gateway could not start this payment. Please try again later.');
        }

        $txn->update([
            'pg_gateway_page_url' => Str::limit($body['GatewayPageURL'], 255, ''),
            'pg_session_key'      => Str::limit((string) ($body['sessionkey'] ?? ''), 50, ''),
        ]);

        return $body['GatewayPageURL'];
    }

    /**
     * Handle a gateway callback. $type: success | fail | cancel | ipn.
     * Returns the transaction (or null if tran_id is unknown).
     */
    public function handleCallback(string $type, array $data): ?Txn
    {
        $tranId = (string) ($data['tran_id'] ?? '');
        $txn = $tranId !== '' ? Txn::where('transaction_id', $tranId)->first() : null;

        if (!$txn) {
            Log::warning('SSLCommerz callback for unknown tran_id', ['type' => $type, 'tran_id' => $tranId]);
            return null;
        }

        $column = $type . '_callback_request';
        $txn->update([$column => Str::limit(json_encode($data), 60000, '')]);

        if ($type === 'fail' || $type === 'cancel') {
            // Never downgrade a payment that was already confirmed.
            Txn::whereKey($txn->id)
                ->where('status', Txn::STATUS_INITIATED)
                ->update([
                    'status'    => $type === 'fail' ? Txn::STATUS_FAILED : Txn::STATUS_CANCELLED,
                    'pg_status' => Str::limit((string) ($data['status'] ?? strtoupper($type)), 20, ''),
                    'remarks'   => isset($data['error']) ? Str::limit((string) $data['error'], 250) : null,
                ]);

            return $txn->fresh();
        }

        // success / ipn: confirm with the validation API before recording anything.
        if (!empty($data['val_id'])) {
            $this->settle($txn, (string) $data['val_id']);
        } elseif ($type === 'ipn' && in_array($data['status'] ?? '', ['FAILED', 'CANCELLED', 'UNATTEMPTED', 'EXPIRED'], true)) {
            Txn::whereKey($txn->id)->where('status', Txn::STATUS_INITIATED)->update([
                'status'    => ($data['status'] ?? '') === 'CANCELLED' ? Txn::STATUS_CANCELLED : Txn::STATUS_FAILED,
                'pg_status' => Str::limit((string) $data['status'], 20, ''),
            ]);
        }

        return $txn->fresh();
    }

    /**
     * Validate with the gateway and, if genuine and matching, record the payment exactly once.
     */
    public function settle(Txn $txn, string $valId): void
    {
        if ($txn->is_successful) {
            return;
        }

        $channel = $txn->channel;

        try {
            $response = $this->http()->get($channel->endpoint('validate_endpoint'), [
                'val_id'       => $valId,
                'store_id'     => $channel->store_id,
                'store_passwd' => $channel->store_password,
                'v'            => 1,
                'format'       => 'json',
            ]);
            $v = $response->json() ?? [];
        } catch (\Throwable $e) {
            // Leave as initiated: the IPN (or a retry of the success page) can settle it later.
            Log::error('SSLCommerz validation call failed', ['tran_id' => $txn->transaction_id, 'error' => $e->getMessage()]);
            return;
        }

        $gwStatus = (string) ($v['status'] ?? '');
        $gwFields = [
            'pg_validation_id'       => Str::limit($valId, 50, ''),
            'pg_status'              => Str::limit($gwStatus, 20, ''),
            'pg_bank_transaction_id' => Str::limit((string) ($v['bank_tran_id'] ?? ''), 50, '') ?: null,
            'pg_transaction_date'    => !empty($v['tran_date']) ? $v['tran_date'] : null,
            'pg_amount'              => isset($v['amount']) ? (float) $v['amount'] : null,
            'pg_store_amount'        => isset($v['store_amount']) ? (float) $v['store_amount'] : null,
            'pg_currency_amount'     => isset($v['currency_amount']) ? Str::limit((string) $v['currency_amount'], 11, '') : null,
            'pg_currency_type'       => isset($v['currency_type']) ? Str::limit((string) $v['currency_type'], 3, '') : null,
            'pg_card_no'             => Str::limit((string) ($v['card_no'] ?? ''), 30, '') ?: null,
            'pg_card_type'           => Str::limit((string) ($v['card_type'] ?? ''), 50, '') ?: null,
            'pg_card_brand'          => Str::limit((string) ($v['card_brand'] ?? ''), 30, '') ?: null,
            'pg_card_issuer'         => Str::limit((string) ($v['card_issuer'] ?? ''), 30, '') ?: null,
        ];

        // 1. Gateway must say the payment is valid.
        $unsettled = fn () => Txn::whereKey($txn->id)->where('status', '!=', Txn::STATUS_SUCCESS);

        if (!in_array($gwStatus, ['VALID', 'VALIDATED'], true)) {
            $unsettled()->update($gwFields + ['status' => Txn::STATUS_WRONG_STATUS, 'remarks' => 'Validation status: ' . ($gwStatus ?: 'none')]);
            return;
        }

        // 2. It must be this transaction, for the same amount, in BDT.
        $sameTran = ($v['tran_id'] ?? null) === $txn->transaction_id;
        $currency = (string) ($v['currency_type'] ?? $v['currency'] ?? '');
        $paidAmount = (float) ($v['currency_amount'] ?? $v['amount'] ?? 0);
        if (!$sameTran || $currency !== config('payments.currency') || abs($paidAmount - (float) $txn->amount) > 0.009) {
            $unsettled()->update($gwFields + [
                'status'  => Txn::STATUS_AMOUNT_MISMATCH,
                'remarks' => Str::limit('Mismatch: tran_id=' . ($v['tran_id'] ?? '?') . " {$currency} {$paidAmount} vs expected BDT {$txn->amount}", 250),
            ]);
            Log::warning('SSLCommerz amount/tran mismatch', ['tran_id' => $txn->transaction_id, 'validation' => $v]);
            return;
        }

        // 3. Gateway flags risky transactions — hold for manual review instead of auto-crediting.
        if ((string) ($v['risk_level'] ?? '0') === '1') {
            $unsettled()->update($gwFields + [
                'status'  => Txn::STATUS_CALLBACK_RECEIVED,
                'remarks' => 'Held by gateway risk check: ' . Str::limit((string) ($v['risk_title'] ?? ''), 200),
            ]);
            return;
        }

        // 4. Record once — lock the transaction and invoice rows.
        DB::transaction(function () use ($txn, $gwFields, $paidAmount) {
            $locked = Txn::whereKey($txn->id)->lockForUpdate()->first();
            if ($locked->is_successful) {
                return; // the other callback got here first
            }

            $invoice = Invoice::whereKey($locked->invoice_id)->lockForUpdate()->first();
            $balanceBefore = $invoice->balance_due;

            $remarks = collect([
                'SSLCommerz',
                $gwFields['pg_card_type'],
                $gwFields['pg_bank_transaction_id'] ? 'bank txn ' . $gwFields['pg_bank_transaction_id'] : null,
                $paidAmount > $balanceBefore + 0.009 ? 'OVERPAID by ৳' . number_format($paidAmount - $balanceBefore, 2) . ' — refund/credit needed' : null,
                $invoice->status === 'cancelled' ? 'Invoice was cancelled — refund needed' : null,
            ])->filter()->implode(' · ');

            $this->invoices->recordOnlinePayment($invoice, $paidAmount, $locked->transaction_id, $remarks);

            $locked->update($gwFields + ['status' => Txn::STATUS_SUCCESS]);
        });
    }

    public function callbackUrl(string $type): string
    {
        $base = rtrim(config('payments.callback_url') ?: url('/'), '/');

        return $base . route('payment.sslcommerz.' . $type, [], false);
    }

    private function newTransactionId(): string
    {
        do {
            $id = 'KC' . now()->format('ymdHis') . strtoupper(Str::random(4)); // 18 chars, column is 20
        } while (Txn::where('transaction_id', $id)->exists());

        return $id;
    }

    private function http()
    {
        return Http::timeout(30)
            ->connectTimeout(10)
            ->acceptJson()
            ->withOptions(['verify' => config('payments.verify_ssl')]);
    }
}
