<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyLog\StoreChildDailyLogRequest;
use App\Http\Requests\DailyLog\UpdateChildDailyLogRequest;
use App\Models\ActivityOccurrence;
use App\Models\Child;
use App\Models\ChildDailyLog;
use App\Models\Staff;
use App\Services\ChildDailyLogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildDailyLogController extends Controller
{
    protected ChildDailyLogService $logService;

    public function __construct(ChildDailyLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Operational listing of daily logs across children.
     */
    public function index(Request $request): View|JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $logs = $this->logService->getPaginatedLogs($request);
        $children = Child::where('is_active', true)->orderBy('first_name')->get();
        $staffMembers = Staff::orderBy('first_name')->get();

        // Get children with activity today for quick jump cards
        $childrenWithLogs = Child::whereHas('dailyLogs', function ($q) use ($date) {
            $q->whereDate('log_date', $date);
        })->withCount(['dailyLogs as today_logs_count' => function ($q) use ($date) {
            $q->whereDate('log_date', $date);
        }])->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'date'    => $date,
                'logs'    => $logs,
            ]);
        }

        return view('admin.daily-logs.index', compact(
            'logs',
            'children',
            'staffMembers',
            'childrenWithLogs',
            'date'
        ));
    }

    /**
     * The dedicated merged nap/meal/activity operational timeline per child per day screen.
     */
    public function childDay(Request $request, Child $child): View|JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $child->load(['parents', 'enrollments.program']);

        $mergedData = $this->logService->getMergedDailyLogForChild($child, $date);

        // Activity occurrences available for this date (to easily link when logging activity)
        $occurrences = ActivityOccurrence::with('activity')
            ->whereDate('occurrence_date', $date)
            ->get();

        $staffMembers = Staff::orderBy('first_name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'child'   => $child,
                'date'    => $date,
                'data'    => $mergedData,
            ]);
        }

        return view('admin.daily-logs.child-day', [
            'child'        => $child,
            'date'         => $date,
            'attendance'   => $mergedData['attendance'],
            'logs'         => $mergedData['logs'],
            'summary'      => $mergedData['summary'],
            'occurrences'  => $occurrences,
            'staffMembers' => $staffMembers,
        ]);
    }

    /**
     * Store a new daily log entry.
     */
    public function store(StoreChildDailyLogRequest $request): RedirectResponse|JsonResponse
    {
        $log = $this->logService->createLog($request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$log->formatted_type} logged successfully.",
                'log'     => $log,
            ]);
        }

        return back()->with('success', "{$log->formatted_type} entry recorded successfully.");
    }

    /**
     * Update an existing daily log entry.
     */
    public function update(UpdateChildDailyLogRequest $request, ChildDailyLog $childDailyLog): RedirectResponse|JsonResponse
    {
        $updated = $this->logService->updateLog($childDailyLog, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$updated->formatted_type} entry updated.",
                'log'     => $updated,
            ]);
        }

        return back()->with('success', "Daily log entry updated successfully.");
    }

    /**
     * Delete a daily log entry.
     */
    public function destroy(ChildDailyLog $childDailyLog): RedirectResponse|JsonResponse
    {
        $type = $childDailyLog->formatted_type;
        $this->logService->deleteLog($childDailyLog);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$type} log entry removed.",
            ]);
        }

        return back()->with('success', "{$type} log entry removed.");
    }
}
