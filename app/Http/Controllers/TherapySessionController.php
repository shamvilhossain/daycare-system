<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ScopesForParent;
use App\Models\Child;
use App\Models\Staff;
use App\Models\TherapyService;
use App\Models\TherapySession;
use App\Services\TherapySessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapySessionController extends Controller
{
    use ScopesForParent;

    protected TherapySessionService $sessionService;

    public function __construct(TherapySessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Display a listing of therapy sessions.
     */
    public function index(Request $request)
    {
        $query = TherapySession::with(['child', 'therapist', 'service', 'bookedBy']);

        // Scope to parent's own children
        $parentChildIds = $this->getParentChildIds();
        if ($parentChildIds !== null) {
            $query->whereIn('child_id', $parentChildIds);
        }

        // Search by child name or therapist name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('child', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('therapist', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Therapy type filter
        if ($therapyType = $request->input('therapy_type')) {
            $query->whereHas('service', function ($q) use ($therapyType) {
                $q->where('therapy_type', $therapyType);
            });
        }

        // Therapist filter
        if ($staffId = $request->input('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        // Date filter
        if ($date = $request->input('session_date')) {
            $query->where('session_date', $date);
        }

        // Statistics (scoped for parents)
        $statsQuery = TherapySession::query();
        if ($parentChildIds !== null) {
            $statsQuery->whereIn('child_id', $parentChildIds);
        }
        $stats = [
            'total'     => (clone $statsQuery)->count(),
            'scheduled' => (clone $statsQuery)->where('status', 'scheduled')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $statsQuery)->where('status', 'cancelled')->count(),
            'no_show'   => (clone $statsQuery)->where('status', 'no_show')->count(),
        ];

        $therapists = Staff::where('role', 'therapist')->where('is_active', true)->orderBy('first_name')->get();
        $sessions = $query->orderBy('session_date', 'desc')->orderBy('start_time', 'desc')->paginate(15)->appends($request->query());

        return view('admin.therapy-sessions.index', compact('sessions', 'therapists', 'stats'));
    }

    /**
     * Show the form for creating a new therapy session.
     */
    public function create()
    {
        if ($this->isParent()) {
            abort(403);
        }

        $children = Child::where('is_active', true)->orderBy('first_name')->get();
        $therapists = Staff::where('role', 'therapist')->where('is_active', true)->orderBy('first_name')->get();
        $services = TherapyService::where('is_active', true)->orderBy('name')->get();

        return view('admin.therapy-sessions.create', compact('children', 'therapists', 'services'));
    }

    /**
     * Store a newly created therapy session.
     */
    public function store(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'child_id'           => 'required|exists:children,id',
            'staff_id'           => 'required|exists:staff,id',
            'therapy_service_id' => 'required|exists:therapy_services,id',
            'session_date'       => 'required|date',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
            'status'             => 'required|in:scheduled,completed,cancelled,no_show',
            'notes'              => 'nullable|string|max:5000',
        ]);

        // Specialization matching
        if (!$this->sessionService->specializationMatches($validated['staff_id'], $validated['therapy_service_id'])) {
            $staff = Staff::find($validated['staff_id']);
            $service = TherapyService::find($validated['therapy_service_id']);
            return back()->withInput()->with('error',
                "Specialization mismatch: {$staff->full_name} is specialized in " . strtoupper($staff->specialization) .
                " but the selected service \"{$service->name}\" requires " . strtoupper($service->therapy_type) . "."
            );
        }

        // Overlap detection
        if ($this->sessionService->hasOverlap($validated['staff_id'], $validated['session_date'], $validated['start_time'], $validated['end_time'])) {
            $staff = Staff::find($validated['staff_id']);
            return back()->withInput()->with('error',
                "Schedule conflict: {$staff->full_name} already has a session booked that overlaps with {$validated['start_time']} – {$validated['end_time']} on {$validated['session_date']}."
            );
        }

        $this->sessionService->createSession($validated, $request->user());

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'Therapy session created successfully.');
    }

    /**
     * Display the specified therapy session.
     */
    public function show(TherapySession $therapySession)
    {
        $this->authorizeParentAccessToChildRecord($therapySession);

        $therapySession->load(['child', 'therapist', 'service', 'bookedBy']);
        return view('admin.therapy-sessions.show', compact('therapySession'));
    }

    /**
     * Show the form for editing a therapy session.
     */
    public function edit(TherapySession $therapySession)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $therapySession->load(['child', 'therapist', 'service']);
        $children = Child::where('is_active', true)->orderBy('first_name')->get();
        $therapists = Staff::where('role', 'therapist')->where('is_active', true)->orderBy('first_name')->get();
        $services = TherapyService::where('is_active', true)->orderBy('name')->get();

        return view('admin.therapy-sessions.edit', compact('therapySession', 'children', 'therapists', 'services'));
    }

    /**
     * Update the specified therapy session.
     */
    public function update(Request $request, TherapySession $therapySession)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'child_id'           => 'required|exists:children,id',
            'staff_id'           => 'required|exists:staff,id',
            'therapy_service_id' => 'required|exists:therapy_services,id',
            'session_date'       => 'required|date',
            'start_time'         => 'required|date_format:H:i',
            'end_time'           => 'required|date_format:H:i|after:start_time',
            'status'             => 'required|in:scheduled,completed,cancelled,no_show',
            'notes'              => 'nullable|string|max:5000',
        ]);

        // Specialization matching
        if (!$this->sessionService->specializationMatches($validated['staff_id'], $validated['therapy_service_id'])) {
            $staff = Staff::find($validated['staff_id']);
            $service = TherapyService::find($validated['therapy_service_id']);
            return back()->withInput()->with('error',
                "Specialization mismatch: {$staff->full_name} is specialized in " . strtoupper($staff->specialization) .
                " but the selected service \"{$service->name}\" requires " . strtoupper($service->therapy_type) . "."
            );
        }

        // Overlap detection (exclude current session)
        if ($this->sessionService->hasOverlap($validated['staff_id'], $validated['session_date'], $validated['start_time'], $validated['end_time'], $therapySession->id)) {
            $staff = Staff::find($validated['staff_id']);
            return back()->withInput()->with('error',
                "Schedule conflict: {$staff->full_name} already has a session booked that overlaps with {$validated['start_time']} – {$validated['end_time']} on {$validated['session_date']}."
            );
        }

        $this->sessionService->updateSession($therapySession, $validated);

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'Therapy session updated successfully.');
    }

    /**
     * Remove the specified therapy session.
     */
    public function destroy(TherapySession $therapySession)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $therapySession->delete();

        return redirect()->route('admin.therapy-sessions.index')
            ->with('success', 'Therapy session deleted successfully.');
    }

    /**
     * Update session status (quick action).
     */
    public function updateStatus(Request $request, TherapySession $therapySession)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
        ]);

        $therapySession->update(['status' => $validated['status']]);

        return back()->with('success', "Session status updated to {$validated['status']}.");
    }
}
