<?php

namespace App\Http\Controllers\Parent;

use App\Models\Invoice;
use App\Services\Payments\SslCommerzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class FeeController extends ParentController
{
    public function index(Request $request): View
    {
        $child = $this->activeChild($request);

        $invoices = $child
            ? $this->invoiceQuery($request)
                ->with('payments')
                ->where('child_id', $child->id)
                ->where('status', '!=', 'cancelled')
                ->orderByDesc('invoice_date')
                ->orderByDesc('id')
                ->paginate(15)
            : null;

        $outstanding = $child
            ? $this->invoiceQuery($request)
                ->with('payments')
                ->where('child_id', $child->id)
                ->whereIn('status', Invoice::OPEN_STATUSES)
                ->get()
                ->sum(fn (Invoice $invoice) => $invoice->balance_due)
            : 0;

        return view('parent.fees.index', compact('invoices', 'outstanding'));
    }

    public function show(Request $request, int $invoice): View
    {
        $invoice = $this->invoiceQuery($request)
            ->with(['child', 'items', 'payments'])
            ->findOrFail($invoice);

        return view('parent.fees.show', compact('invoice'));
    }

    public function pay(Request $request, int $invoice, SslCommerzService $gateway): View
    {
        $invoice = $this->invoiceQuery($request)
            ->with(['child', 'payments'])
            ->where('status', '!=', 'cancelled')
            ->findOrFail($invoice);

        $onlinePaymentEnabled = $gateway->isEnabled();
        $payableAmount = $gateway->payableAmount($invoice);

        return view('parent.fees.pay', compact('invoice', 'onlinePaymentEnabled', 'payableAmount'));
    }

    public function startPayment(Request $request, int $invoice, SslCommerzService $gateway): RedirectResponse
    {
        $data = $request->validate(['method' => ['required', 'in:' . implode(',', array_keys(SslCommerzService::METHODS))]]);

        $invoice = $this->invoiceQuery($request)->findOrFail($invoice);

        try {
            return redirect()->away($gateway->initiate($invoice, $data['method']));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Invoices billed to this parent — same rule as the admin InvoiceController (parent_id).
     */
    private function invoiceQuery(Request $request)
    {
        return $this->parentProfile($request)->invoices()->getQuery();
    }
}
