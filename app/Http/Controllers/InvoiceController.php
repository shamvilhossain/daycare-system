<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ScopesForParent;
use App\Models\Invoice;
use App\Models\InvoiceItemPreset;
use App\Models\Child;
use App\Models\TherapyPackage;
use App\Models\TherapyService;
use App\Models\TherapySession;
use App\Models\ParentProfile;
use App\Services\InvoiceService;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    use ScopesForParent;

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

        // Scope to parent's own invoices
        $parentProfileId = $this->getParentProfileId();
        if ($parentProfileId !== null) {
            $query->where('parent_id', $parentProfileId);
        }

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

        // Statistics (scoped for parents)
        $statsQuery = Invoice::query();
        if ($parentProfileId !== null) {
            $statsQuery->where('parent_id', $parentProfileId);
        }
        $stats = [
            'total'          => (clone $statsQuery)->count(),
            'draft'          => (clone $statsQuery)->where('status', 'draft')->count(),
            'sent'           => (clone $statsQuery)->where('status', 'sent')->count(),
            'partially_paid' => (clone $statsQuery)->where('status', 'partially_paid')->count(),
            'paid'           => (clone $statsQuery)->where('status', 'paid')->count(),
            'overdue'        => (clone $statsQuery)->where('status', 'overdue')->count(),
            'cancelled'      => (clone $statsQuery)->where('status', 'cancelled')->count(),
        ];

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        if ($this->isParent()) {
            abort(403);
        }

        $parents = ParentProfile::orderBy('first_name')->get();
        return view('admin.invoices.create', [
            'parents'     => $parents,
            'itemCatalog' => $this->itemCatalog(),
        ]);
    }

    /**
     * Options for the line-item Description dropdown: saved free-text items,
     * therapy packages (bought for the child on save) and single therapy sessions.
     */
    private function itemCatalog(): array
    {
        $presets = InvoiceItemPreset::forDropdown()->get()->map(fn ($p) => [
            'kind'   => 'preset',
            'name'   => $p->name,
            'amount' => $p->default_amount !== null ? (float) $p->default_amount : null,
        ]);

        $packages = TherapyPackage::where('is_active', true)->with('items.service')->orderBy('name')->get()->map(fn ($p) => [
            'kind'   => 'package',
            'id'     => $p->id,
            'name'   => $p->name,
            'amount' => (float) $p->price,
            'detail' => $p->items->map(fn ($i) => $i->session_count . '× ' . ($i->service?->name ?? 'session'))->implode(' + '),
        ]);

        $services = TherapyService::where('is_active', true)->orderBy('name')->get()->map(fn ($s) => [
            'kind'   => 'service',
            'id'     => $s->id,
            'name'   => $s->name . ' — single session',
            'amount' => (float) $s->session_rate,
            'detail' => strtoupper($s->therapy_type) . ' · ' . $s->duration_minutes . ' min',
        ]);

        return $presets->concat($packages)->concat($services)->values()->all();
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'parent_id'              => 'required|exists:parents,id',
            'child_id'               => 'required|exists:children,id',
            'invoice_date'           => 'required|date',
            'due_date'               => 'required|date|after_or_equal:invoice_date',
            'items'                  => 'nullable|array',
            'items.*.description'    => 'required_with:items|string|max:255',
            'items.*.amount'         => 'required_with:items|numeric|min:0.01',
            'items.*.therapy_package_id' => 'nullable|integer|exists:therapy_packages,id',
            'items.*.therapy_service_id' => 'nullable|integer|exists:therapy_services,id',
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
        // Parents can only view their own invoices
        $parentProfileId = $this->getParentProfileId();
        if ($parentProfileId !== null && $invoice->parent_id !== $parentProfileId) {
            abort(403, 'You do not have access to this invoice.');
        }

        $invoice->load(['parent', 'child', 'items', 'payments.receivedBy', 'onlineTransactions']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Download the invoice as a PDF (same access rule as show()).
     */
    public function pdf(Invoice $invoice, InvoicePdfService $pdf)
    {
        $parentProfileId = $this->getParentProfileId();
        if ($parentProfileId !== null && $invoice->parent_id !== $parentProfileId) {
            abort(403, 'You do not have access to this invoice.');
        }

        return response($pdf->render($invoice), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdf->filename($invoice) . '"',
            'Cache-Control'       => 'private, no-store',
        ]);
    }

    /**
     * Record a payment against an invoice.
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'paid_amount'     => 'required|numeric|min:0.01',
            'payment_method'  => 'required|in:cash,card,bank_transfer,online',
            'transaction_id'  => 'nullable|string|max:255',
            'paid_at'         => 'required|date',
            'remarks'         => 'nullable|string|max:1000',
            'penalty_amount'  => 'nullable|numeric|min:0',
        ]);

        if (!$invoice->is_open) {
            return back()->with('error', 'Payments can only be recorded on unpaid, non-cancelled invoices.');
        }

        $this->invoiceService->addPayment($invoice, $validated, $request->user());

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Cancel an invoice.
     */
    public function cancel(Invoice $invoice)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $this->invoiceService->cancelInvoice($invoice);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} has been cancelled.");
    }

    /**
     * Destroy (delete) an invoice.
     */
    public function destroy(Invoice $invoice)
    {
        if ($this->isParent()) {
            abort(403);
        }

        DB::transaction(function () use ($invoice) {
            // Packages bought on this invoice would otherwise stay active but unbilled
            $this->invoiceService->cancelPackagesBilledOn($invoice);
            $invoice->delete();
        });

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
            ->whereNull('child_therapy_package_id') // prepaid via a therapy package — never bill again
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
