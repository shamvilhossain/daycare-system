<?php

namespace App\Http\Controllers\Parent;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
                ->whereIn('status', ['draft', 'overdue'])
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

    public function pay(Request $request, int $invoice): View
    {
        $invoice = $this->invoiceQuery($request)
            ->with(['child', 'payments'])
            ->where('status', '!=', 'cancelled')
            ->findOrFail($invoice);

        // Online gateway (bKash / Nagad / SSLCommerz via payment_channels) is not wired yet.
        $onlinePaymentEnabled = false;

        return view('parent.fees.pay', compact('invoice', 'onlinePaymentEnabled'));
    }

    /**
     * Invoices billed to this parent — same rule as the admin InvoiceController (parent_id).
     */
    private function invoiceQuery(Request $request)
    {
        return $this->parentProfile($request)->invoices()->getQuery();
    }
}
