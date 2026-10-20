<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentLinkService;
use App\Services\Payments\SslCommerzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Endpoints SSLCommerz POSTs to. These routes run WITHOUT session, cookies or CSRF
 * (see routes/payments.php): the cross-site POST carries no login cookie, and starting
 * a fresh session here would overwrite a logged-in parent's cookie.
 * Everything is looked up by tran_id; nothing depends on who is logged in.
 */
class SslCommerzCallbackController extends Controller
{
    public function __construct(private SslCommerzService $gateway, private PaymentLinkService $links)
    {
    }

    public function success(Request $request): RedirectResponse|Response
    {
        return $this->browserReturn('success', $request);
    }

    public function fail(Request $request): RedirectResponse|Response
    {
        return $this->browserReturn('fail', $request);
    }

    public function cancel(Request $request): RedirectResponse|Response
    {
        return $this->browserReturn('cancel', $request);
    }

    /**
     * Server-to-server Instant Payment Notification.
     */
    public function ipn(Request $request): Response
    {
        $txn = $this->gateway->handleCallback('ipn', $request->all());

        return response($txn ? 'OK' : 'UNKNOWN TRANSACTION', $txn ? 200 : 404)
            ->header('Content-Type', 'text/plain');
    }

    private function browserReturn(string $type, Request $request): RedirectResponse|Response
    {
        $txn = $this->gateway->handleCallback($type, $request->all());

        if (!$txn) {
            return response()->view('payments.link-invalid', ['message' => 'We could not find this payment.'], 404);
        }

        // Post/Redirect/Get: the receipt is a normal signed GET page.
        return redirect()->away($this->links->receiptLink($txn));
    }
}
