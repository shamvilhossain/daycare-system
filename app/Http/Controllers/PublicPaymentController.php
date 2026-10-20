<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentOnlineTransaction;
use App\Services\Payments\SslCommerzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

/**
 * Public payment link (/pay/{invoice}?signature=...) — no login needed.
 * Also shows the receipt / result page after the gateway returns.
 */
class PublicPaymentController extends Controller
{
    public function __construct(private SslCommerzService $gateway)
    {
    }

    public function show(Request $request, int $invoice): View|Response
    {
        if (!$request->hasValidRelativeSignature()) {
            return response()->view('payments.link-invalid', [], 403);
        }

        $invoice = Invoice::with(['child', 'parent', 'items', 'payments'])->findOrFail($invoice);

        return view('payments.pay', [
            'invoice'    => $invoice,
            'amount'     => $this->gateway->payableAmount($invoice),
            'enabled'    => $this->gateway->isEnabled(),
            'formAction' => $request->fullUrl(),
        ]);
    }

    public function start(Request $request, int $invoice): RedirectResponse|Response
    {
        if (!$request->hasValidRelativeSignature()) {
            return response()->view('payments.link-invalid', [], 403);
        }

        $data = $request->validate(['method' => ['required', 'in:' . implode(',', array_keys(SslCommerzService::METHODS))]]);
        $invoice = Invoice::findOrFail($invoice);

        try {
            return redirect()->away($this->gateway->initiate($invoice, $data['method']));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Receipt / result page for one online transaction (signed link, no login).
     */
    public function receipt(Request $request, string $transaction): View|Response
    {
        if (!$request->hasValidRelativeSignature()) {
            return response()->view('payments.link-invalid', [], 403);
        }

        $txn = PaymentOnlineTransaction::with(['invoice.child', 'invoice.parent', 'invoice.payments'])
            ->where('transaction_id', $transaction)
            ->firstOrFail();

        return view('payments.receipt', [
            'txn'     => $txn,
            'invoice' => $txn->invoice,
        ]);
    }
}
