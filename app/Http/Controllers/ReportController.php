<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityOccurrence;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Admin's monthly "which activities happened each day" calendar view.
     */
    public function activityCalendar(Request $request): View|JsonResponse
    {
        $year = (int)$request->query('year', Carbon::today()->year);
        $month = (int)$request->query('month', Carbon::today()->month);

        // Clamp year and month to valid ranges
        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));

        $currentMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        // Filters
        $programId = $request->query('program_id');
        $category = $request->query('category');
        $activityId = $request->query('activity_id');
        $status = $request->query('status');

        $query = ActivityOccurrence::with(['activity', 'program', 'staff.user', 'media'])
            ->withCount('childDailyLogs')
            ->whereBetween('occurrence_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($category) {
            $query->whereHas('activity', function ($q) use ($category) {
                $q->where('category', $category);
            });
        }

        if ($activityId) {
            $query->where('activity_id', $activityId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $occurrences = $query->orderBy('occurrence_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Group occurrences by day date string 'YYYY-MM-DD'
        $occurrencesByDate = $occurrences->groupBy(function ($occ) {
            return $occ->occurrence_date->toDateString();
        });

        // Monthly statistics & analytics
        $totalActivities = $occurrences->count();
        $completedCount = $occurrences->where('status', 'completed')->count();
        $partialCount = $occurrences->where('status', 'partial')->count();
        $plannedCount = $occurrences->where('status', 'planned')->count();
        $cancelledCount = $occurrences->where('status', 'cancelled')->count();
        $daysWithActivities = $occurrencesByDate->count();

        // Top category calculation
        $categoryCounts = $occurrences->groupBy(function ($occ) {
            return $occ->activity ? $occ->activity->category_label : 'Other';
        })->map->count()->sortDesc();
        $topCategory = $categoryCounts->keys()->first() ?? 'None';

        // Most active program
        $programCounts = $occurrences->groupBy(function ($occ) {
            return $occ->program ? $occ->program->name : 'General';
        })->map->count()->sortDesc();
        $topProgram = $programCounts->keys()->first() ?? 'None';

        $totalChildrenEngaged = $occurrences->sum('child_daily_logs_count');

        $stats = [
            'total'                  => $totalActivities,
            'completed'              => $completedCount,
            'partial'                => $partialCount,
            'planned'                => $plannedCount,
            'cancelled'              => $cancelledCount,
            'days_active'            => $daysWithActivities,
            'total_days_in_month'    => $endOfMonth->day,
            'top_category'           => $topCategory,
            'top_program'            => $topProgram,
            'total_children_engaged' => $totalChildrenEngaged,
        ];

        // Construct 7-day grid (Sunday to Saturday)
        $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $calendarDays = [];
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $dateStr = $cursor->toDateString();
            $dayOccurrences = $occurrencesByDate->get($dateStr, collect());

            $calendarDays[] = [
                'date'              => $dateStr,
                'day_number'        => $cursor->day,
                'is_current_month'  => $cursor->month === $month,
                'is_today'          => $cursor->isToday(),
                'is_weekend'        => $cursor->isWeekend(),
                'occurrences'       => $dayOccurrences,
                'occurrences_count' => $dayOccurrences->count(),
            ];

            $cursor->addDay();
        }

        // Filter options
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $activities = Activity::orderBy('name')->get();
        $categories = [
            'art'          => 'Art',
            'music'        => 'Music',
            'outdoor'      => 'Outdoor',
            'reading'      => 'Reading',
            'motor_skills' => 'Motor Skills',
            'sensory'      => 'Sensory',
            'cognitive'    => 'Cognitive',
            'social'       => 'Social',
            'language'     => 'Language',
            'math'         => 'Math',
            'science'      => 'Science',
            'other'        => 'Other',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success'             => true,
                'month'               => $month,
                'year'                => $year,
                'month_name'          => $currentMonth->format('F Y'),
                'stats'               => $stats,
                'calendar_days'       => $calendarDays,
                'occurrences_by_date' => $occurrencesByDate,
            ]);
        }

        return view('admin.reports.activity-calendar', compact(
            'currentMonth',
            'month',
            'year',
            'prevMonth',
            'nextMonth',
            'calendarDays',
            'occurrencesByDate',
            'stats',
            'programs',
            'activities',
            'categories',
            'programId',
            'category',
            'activityId',
            'status'
        ));
    }
}
