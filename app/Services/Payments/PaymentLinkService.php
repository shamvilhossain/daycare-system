<?php

namespace App\Services\Payments;

use App\Models\Invoice;
use App\Models\PaymentOnlineTransaction;
use Illuminate\Support\Facades\URL;

/**
 * Builds the public, signed links parents use without logging in.
 *
 * Signatures are "relative" (path + query only), so a link stays valid whether it is
 * opened via the real domain, a tunnel or a sub-folder install.
 */
class PaymentLinkService
{
    public function invoiceLink(Invoice $invoice): string
    {
        $path = URL::temporarySignedRoute(
            'pay.show',
            now()->addDays(config('payments.link_valid_days')),
            ['invoice' => $invoice->id],
            false
        );

        return $this->base(config('payments.public_url')) . $path;
    }

    public function receiptLink(PaymentOnlineTransaction $txn): string
    {
        $path = URL::temporarySignedRoute(
            'payment.receipt',
            now()->addDays(config('payments.receipt_valid_days')),
            ['transaction' => $txn->transaction_id],
            false
        );

        // Receipt opens in the same browser that returned from the gateway (the callback host).
        return $this->base(config('payments.callback_url') ?: config('payments.public_url')) . $path;
    }

    /**
     * Ready-to-send message for SMS / WhatsApp.
     */
    public function message(Invoice $invoice, string $link): string
    {
        $invoice->loadMissing(['parent', 'child', 'payments']);

        return sprintf(
            "Dear %s, invoice %s for %s: ৳%s due %s. Pay securely online: %s\n— %s",
            $invoice->parent?->first_name ?: 'Parent',
            $invoice->invoice_number,
            $invoice->child?->first_name ?? 'your child',
            number_format($invoice->balance_due, 2),
            $invoice->due_date->format('j M Y'),
            $link,
            config('parentapp.organization.name')
        );
    }

    /**
     * Bangladeshi mobile → international digits for wa.me (e.g. 01712345678 → 8801712345678).
     */
    public function whatsappNumber(?string $mobile): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $mobile);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '880')) {
            return $digits;
        }
        if (str_starts_with($digits, '0')) {
            return '88' . $digits;
        }

        return strlen($digits) === 10 ? '880' . $digits : $digits;
    }

    private function base(?string $configured): string
    {
        return rtrim($configured ?: url('/'), '/');
    }
}
