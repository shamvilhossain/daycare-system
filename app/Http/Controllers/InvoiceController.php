<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Child;
use App\Models\TherapySession;
use App\Models\ParentProfile;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['parent', 'child']);

        // Search by parent name, child name, or invoice number
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('parent', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('child', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Date range filter
        if ($from = $request->input('from_date')) {
            $query->whereDate('invoice_date', '>=', $from);
        }
        if ($to = $request->input('to_date')) {
            $query->whereDate('invoice_date', '<=', $to);
        }

        // Statistics
        $stats = [
            'total'     => Invoice::count(),
            'draft'     => Invoice::where('status', 'draft')->count(),
            'paid'      => Invoice::where('status', 'paid')->count(),
            'overdue'   => Invoice::where('status', 'overdue')->count(),
            'cancelled' => Invoice::where('status', 'cancelled')->count(),
        ];

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $parents = ParentProfile::orderBy('first_name')->get();
        return view('admin.invoices.create', compact('parents'));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id'              => 'required|exists:parents,id',
            'child_id'               => 'required|exists:children,id',
            'invoice_date'           => 'required|date',
            'due_date'               => 'required|date|after_or_equal:invoice_date',
            'items'                  => 'nullable|array',
            'items.*.description'    => 'required_with:items|string|max:255',
            'items.*.amount'         => 'required_with:items|numeric|min:0.01',
            'therapy_session_ids'    => 'nullable|array',
            'therapy_session_ids.*'  => 'exists:therapy_sessions,id',
        ]);

        // At least one of manual items or therapy sessions must be present
        if (empty($validated['items']) && empty($validated['therapy_session_ids'])) {
            return back()->withErrors(['items' => 'Please add at least one line item or select therapy sessions to bill.'])->withInput();
        }

        $invoice = $this->invoiceService->createInvoice($validated, $request->user());

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} created successfully.");
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['parent', 'child', 'items', 'payments.receivedBy']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Record a payment against an invoice.
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'paid_amount'     => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:cash,card,bank_transfer,online',
            'transaction_id'  => 'nullable|string|max:255',
            'paid_at'         => 'required|date',
            'remarks'         => 'nullable|string|max:1000',
            'penalty_amount'  => 'nullable|numeric|min:0',
        ]);

        $this->invoiceService->addPayment($invoice, $validated, $request->user());

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Cancel an invoice.
     */
    public function cancel(Invoice $invoice)
    {
        $this->invoiceService->cancelInvoice($invoice);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} has been cancelled.");
    }

    /**
     * Destroy (delete) an invoice.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * API: Get children for a given parent (used in create form).
     */
    public function getChildrenByParent(Request $request)
    {
        $parent = ParentProfile::findOrFail($request->parent_id);
        $children = $parent->children()->select('children.id', 'children.first_name', 'children.last_name')->get();

        return response()->json($children);
    }

    /**
     * API: Get billable therapy sessions for a child.
     *
     * Returns completed sessions that have NOT been billed yet
     * (whereDoesntHave('invoiceItem') dedup guard).
     */
    public function getBillableTherapySessions(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
        ]);

        $sessions = TherapySession::with(['service', 'therapist'])
            ->where('child_id', $request->child_id)
            ->where('status', 'completed')
            ->whereDoesntHave('invoiceItem') // skip already-billed sessions
            ->orderBy('session_date', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id'            => $session->id,
                    'session_date'  => $session->session_date->format('M d, Y'),
                    'service_name'  => $session->service->name,
                    'therapy_type'  => strtoupper($session->service->therapy_type),
                    'therapist'     => $session->therapist->first_name . ' ' . $session->therapist->last_name,
                    'session_rate'  => number_format($session->service->session_rate, 2),
                    'raw_rate'      => (float) $session->service->session_rate,
                ];
            });

        return response()->json($sessions);
    }
}
