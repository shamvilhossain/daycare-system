<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ScopesForParent;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Program;
use App\Services\AdmissionFeeService;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    use ScopesForParent;

    protected EnrollmentService $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['child.admissionInvoice', 'program', 'createdBy', 'approvedBy']);

        // Scope to parent's own children
        $parentChildIds = $this->getParentChildIds();
        if ($parentChildIds !== null) {
            $query->whereIn('child_id', $parentChildIds);
        }

        // Search by child name or program name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('child', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('program', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Program filter
        if ($programId = $request->input('program_id')) {
            $query->where('program_id', $programId);
        }

        // Child filter
        if ($childId = $request->input('child_id')) {
            $query->where('child_id', $childId);
        }

        // Statistics (scoped for parents)
        $statsQuery = Enrollment::query();
        if ($parentChildIds !== null) {
            $statsQuery->whereIn('child_id', $parentChildIds);
        }
        $stats = [
            'total'     => (clone $statsQuery)->count(),
            'active'    => (clone $statsQuery)->where('status', 'active')->count(),
            'pending'   => (clone $statsQuery)->where('status', 'pending')->count(),
            'graduated' => (clone $statsQuery)->where('status', 'graduated')->count(),
            'withdrawn' => (clone $statsQuery)->where('status', 'withdrawn')->count(),
        ];

        $programs = Program::orderBy('name')->get();
        $enrollments = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        return view('admin.enrollments.index', compact('enrollments', 'programs', 'stats'));
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function create(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $children = Child::where('is_active', true)->with(['parents', 'admissionInvoice'])->orderBy('first_name')->get();
        $programs = Program::where('is_active', true)->withCount(['enrollments as active_count' => function ($q) {
            $q->whereIn('status', ['active', 'pending']);
        }])->orderBy('name')->get();

        $selectedChildId = $request->query('child_id');
        $selectedProgramId = $request->query('program_id');

        // Admission fee panel: what each child would be charged on this enrolment
        $admissionFee = app(AdmissionFeeService::class)->fee();
        $admission = $children->mapWithKeys(fn (Child $c) => [$c->id => [
            'status'     => $admissionFee > 0 || $c->is_admitted ? $c->admission_status : 'off',
            'invoice'    => $c->admissionInvoice?->invoice_number,
            'has_parent' => $c->parents->isNotEmpty(),
            'reason'     => $c->admission_fee_waiver_reason,
        ]]);

        return view('admin.enrollments.create', compact('children', 'programs', 'selectedChildId', 'selectedProgramId', 'admissionFee', 'admission'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'child_id'     => 'required|exists:children,id',
            'program_id'   => 'required|exists:programs,id',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'status'       => 'required|in:pending,active,withdrawn,graduated,rejected',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'notes'        => 'nullable|string|max:2000',
            'waive_admission_fee'     => 'nullable|boolean',
            'admission_waiver_reason' => 'nullable|required_if:waive_admission_fee,1|string|max:255',
        ]);

        $enrollment = $this->enrollmentService->createEnrollment($validated, $request->user());
        $invoice = $enrollment->admissionInvoice;
        $message = "Child successfully enrolled into {$enrollment->program->name}.";

        if ($invoice) {
            // Straight to the admission invoice so the payment link can be sent
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', "{$message} Admission invoice {$invoice->invoice_number} created for ৳" . number_format((float) $invoice->total_amount, 2)
                    . ' — send the payment link to the parent, then approve the enrolment once it is paid.')
                ->with('warning', $enrollment->status === 'active' ? 'The enrolment was set Active before the admission invoice was paid.' : null);
        }

        return redirect()->route('admin.enrollments.index')->with('success', $message);
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        $this->authorizeParentAccessToChildRecord($enrollment);

        $enrollment->load(['child.parents', 'child.admissionInvoice', 'program', 'createdBy', 'approvedBy']);
        $admissionWarning = $enrollment->status === 'pending' && $enrollment->child
            ? app(AdmissionFeeService::class)->approvalWarning($enrollment->child) : null;

        return view('admin.enrollments.show', compact('enrollment', 'admissionWarning'));
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit(Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $enrollment->load(['child', 'program']);
        $children = Child::orderBy('first_name')->get();
        $programs = Program::withCount(['enrollments as active_count' => function ($q) {
            $q->whereIn('status', ['active', 'pending']);
        }])->orderBy('name')->get();

        return view('admin.enrollments.edit', compact('enrollment', 'children', 'programs'));
    }

    /**
     * Update the specified enrollment in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'child_id'     => 'required|exists:children,id',
            'program_id'   => 'required|exists:programs,id',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'status'       => 'required|in:pending,active,withdrawn,graduated,rejected',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $wasActive = $enrollment->status === 'active';
        $this->enrollmentService->updateEnrollment($enrollment, $validated, $request->user());
        $invoice = $enrollment->relationLoaded('admissionInvoice') ? $enrollment->admissionInvoice : null;
        $warning = !$wasActive && $enrollment->status === 'active'
            ? app(AdmissionFeeService::class)->approvalWarning($enrollment->child()->first()) : null;

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment record updated successfully.'
                . ($invoice ? " Admission invoice {$invoice->invoice_number} created." : ''))
            ->with('warning', $warning ? "Set Active without the admission fee: {$warning}" : null);
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $cancelled = $this->enrollmentService->delete($enrollment);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment record deleted successfully.'
                . ($cancelled ? " Unpaid admission invoice {$cancelled->invoice_number} was cancelled." : ''));
    }

    /**
     * Approve a pending enrollment.
     */
    public function approve(Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $this->enrollmentService->approve($enrollment, auth()->user());
        $warning = app(AdmissionFeeService::class)->approvalWarning($enrollment->child()->first());

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been approved.")
            ->with('warning', $warning ? "Approved without the admission fee: {$warning}" : null);
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject(Request $request, Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $reason = $request->input('reason');
        $this->enrollmentService->reject($enrollment, auth()->user(), $reason);
        $cancelled = $enrollment->admissionInvoice;

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been rejected."
            . ($cancelled ? " Unpaid admission invoice {$cancelled->invoice_number} was cancelled." : ''));
    }

    /**
     * Withdraw an enrollment.
     */
    public function withdraw(Request $request, Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $endDate = $request->input('end_date');
        $this->enrollmentService->withdraw($enrollment, $endDate);

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been marked as withdrawn.");
    }

    /**
     * Graduate an enrollment.
     */
    public function graduate(Request $request, Enrollment $enrollment)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $endDate = $request->input('end_date');
        $this->enrollmentService->graduate($enrollment, $endDate);

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been marked as graduated.");
    }
}
