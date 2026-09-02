<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Program;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
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
        $query = Enrollment::with(['child', 'program', 'createdBy', 'approvedBy']);

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

        // Statistics
        $stats = [
            'total'     => Enrollment::count(),
            'active'    => Enrollment::where('status', 'active')->count(),
            'pending'   => Enrollment::where('status', 'pending')->count(),
            'graduated' => Enrollment::where('status', 'graduated')->count(),
            'withdrawn' => Enrollment::where('status', 'withdrawn')->count(),
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
        $children = Child::where('is_active', true)->orderBy('first_name')->get();
        $programs = Program::where('is_active', true)->withCount(['enrollments as active_count' => function ($q) {
            $q->whereIn('status', ['active', 'pending']);
        }])->orderBy('name')->get();

        $selectedChildId = $request->query('child_id');
        $selectedProgramId = $request->query('program_id');

        return view('admin.enrollments.create', compact('children', 'programs', 'selectedChildId', 'selectedProgramId'));
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id'     => 'required|exists:children,id',
            'program_id'   => 'required|exists:programs,id',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'status'       => 'required|in:pending,active,withdrawn,graduated,rejected',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $enrollment = $this->enrollmentService->createEnrollment($validated, $request->user());

        return redirect()->route('admin.enrollments.index')
            ->with('success', "Child successfully enrolled into {$enrollment->program->name}.");
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['child.parents', 'program', 'createdBy', 'approvedBy']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified enrollment.
     */
    public function edit(Enrollment $enrollment)
    {
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
        $validated = $request->validate([
            'child_id'     => 'required|exists:children,id',
            'program_id'   => 'required|exists:programs,id',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'status'       => 'required|in:pending,active,withdrawn,graduated,rejected',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $this->enrollmentService->updateEnrollment($enrollment, $validated, $request->user());

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment record updated successfully.');
    }

    /**
     * Remove the specified enrollment from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment record deleted successfully.');
    }

    /**
     * Approve a pending enrollment.
     */
    public function approve(Enrollment $enrollment)
    {
        $this->enrollmentService->approve($enrollment, auth()->user());

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been approved.");
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject(Request $request, Enrollment $enrollment)
    {
        $reason = $request->input('reason');
        $this->enrollmentService->reject($enrollment, auth()->user(), $reason);

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been rejected.");
    }

    /**
     * Withdraw an enrollment.
     */
    public function withdraw(Request $request, Enrollment $enrollment)
    {
        $endDate = $request->input('end_date');
        $this->enrollmentService->withdraw($enrollment, $endDate);

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been marked as withdrawn.");
    }

    /**
     * Graduate an enrollment.
     */
    public function graduate(Request $request, Enrollment $enrollment)
    {
        $endDate = $request->input('end_date');
        $this->enrollmentService->graduate($enrollment, $endDate);

        return back()->with('success', "Enrollment for {$enrollment->child->full_name} has been marked as graduated.");
    }
}
