<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Services\Payments\PaymentLinkService;
use App\Services\Payments\SslCommerzService;
use Illuminate\Http\RedirectResponse;

/**
 * Admin: create a signed payment link for an invoice and hand it to the admin
 * to send by SMS, WhatsApp or copy-paste.
 */
class PaymentLinkController extends Controller
{
    public function store(Invoice $invoice, PaymentLinkService $links, SslCommerzService $gateway, InvoiceService $invoices): RedirectResponse
    {
        if (!$gateway->isEnabled()) {
            return back()->with('error', 'No active payment channel. Activate one in payment_channels first.');
        }

        if ($gateway->payableAmount($invoice) === null) {
            return back()->with('error', $invoice->is_open
                ? 'The balance due is below the online minimum of ৳' . config('payments.min_amount') . '.'
                : 'Only unpaid, non-cancelled invoices can be paid online.');
        }

        $invoice->load(['parent', 'child', 'payments']);
        $url = $links->invoiceLink($invoice);
        $message = $links->message($invoice, $url);
        $phone = $links->whatsappNumber($invoice->parent?->mobile);

        $invoices->markSent($invoice);

        return back()->with('payment_link', [
            'url'        => $url,
            'message'    => $message,
            'whatsapp'   => $phone ? 'https://wa.me/' . $phone . '?text=' . rawurlencode($message) : null,
            'sms'        => $invoice->parent?->mobile ? 'sms:' . preg_replace('/[^\d+]/', '', $invoice->parent->mobile) . '?body=' . rawurlencode($message) : null,
            'expires_at' => now()->addDays(config('payments.link_valid_days'))->format('M d, Y'),
        ]);
    }
}
