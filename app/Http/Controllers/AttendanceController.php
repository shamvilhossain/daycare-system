<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\BulkAttendanceRequest;
use App\Http\Requests\Attendance\CheckInRequest;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Program;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display the daily operational Check-in / Check-out desk.
     */
    public function index(Request $request): View|JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $programId = $request->query('program_id') ? (int)$request->query('program_id') : null;
        $search = $request->query('search');
        $statusFilter = $request->query('status');

        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $roster = $this->attendanceService->getRoster($date, $programId, $search, $statusFilter);
        $stats = $this->attendanceService->getStats($date, $programId);
        $allChildren = Child::where('is_active', true)->orderBy('first_name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'date'    => $date,
                'stats'   => $stats,
                'roster'  => $roster,
            ]);
        }

        return view('admin.attendance.index', compact(
            'roster',
            'stats',
            'programs',
            'allChildren',
            'date',
            'programId',
            'search',
            'statusFilter'
        ));
    }

    /**
     * Quick 1-click Check-in for a child.
     */
    public function checkIn(CheckInRequest $request): RedirectResponse|JsonResponse
    {
        $attendance = $this->attendanceService->checkIn($request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$attendance->child->full_name} checked in successfully.",
                'attendance' => $attendance,
            ]);
        }

        return back()->with('success', "{$attendance->child->full_name} has been checked in successfully.");
    }

    /**
     * Quick 1-click Check-out for an attendance record.
     */
    public function checkOut(Request $request, Attendance $attendance): RedirectResponse|JsonResponse
    {
        $request->validate([
            'check_out_time' => 'nullable|date_format:H:i,H:i:s',
            'notes'          => 'nullable|string|max:500',
        ]);

        $updated = $this->attendanceService->checkOut(
            $attendance,
            $request->input('check_out_time'),
            $request->input('notes')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$updated->child->full_name} checked out successfully.",
                'attendance' => $updated,
            ]);
        }

        return back()->with('success', "{$updated->child->full_name} checked out at {$updated->formatted_check_out_time}. Total stay: {$updated->formatted_duration}.");
    }

    /**
     * Mark a child absent or excused.
     */
    public function markAbsent(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'child_id'        => 'required|exists:children,id',
            'program_id'      => 'required|exists:programs,id',
            'attendance_date' => 'required|date',
            'status'          => 'required|in:absent,excused',
            'notes'           => 'nullable|string|max:500',
        ]);

        $attendance = $this->attendanceService->markAbsent(
            $validated['child_id'],
            $validated['program_id'],
            $validated['attendance_date'],
            $validated['notes'] ?? null,
            $validated['status']
        );

        $statusLabel = ucfirst($validated['status']);

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$attendance->child->full_name} marked as {$statusLabel}.",
                'attendance' => $attendance,
            ]);
        }

        return back()->with('success', "{$attendance->child->full_name} marked as {$statusLabel}.");
    }

    /**
     * Manual store of an attendance record.
     */
    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $attendance = $this->attendanceService->createOrUpdate($request->validated());

        return back()->with('success', "Attendance record saved for {$attendance->child->full_name}.");
    }

    /**
     * Update an existing attendance record.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        $attendance->update($request->validated());

        return back()->with('success', "Attendance updated for {$attendance->child->full_name}.");
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $childName = $attendance->child ? $attendance->child->full_name : 'child';
        $attendance->delete();

        return back()->with('success', "Attendance record for {$childName} was removed.");
    }

    /**
     * Bulk check-in or check-out for selected children.
     */
    public function bulk(BulkAttendanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $action = $validated['action'];
        $date = $validated['attendance_date'];
        $time = $validated['time'] ?? null;
        $notes = $validated['notes'] ?? null;

        if ($action === 'check_in') {
            if (empty($validated['child_ids']) || empty($validated['program_id'])) {
                return back()->withErrors(['child_ids' => 'Please select at least one child and a program for bulk check-in.']);
            }
            $count = $this->attendanceService->bulkCheckIn($validated['child_ids'], $validated['program_id'], $date, $time, $notes);
            return back()->with('success', "Successfully checked in {$count} children.");
        } elseif ($action === 'check_out') {
            if (empty($validated['attendance_ids'])) {
                return back()->withErrors(['attendance_ids' => 'Please select at least one checked-in child to check out.']);
            }
            $count = $this->attendanceService->bulkCheckOut($validated['attendance_ids'], $time, $notes);
            return back()->with('success', "Successfully checked out {$count} children.");
        }

        return back();
    }
}
