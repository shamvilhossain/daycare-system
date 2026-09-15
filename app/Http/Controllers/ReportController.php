<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityOccurrence;
use App\Models\Invoice;
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

    /**
     * Billing / Revenue report: What's invoiced vs collected.
     */
    public function billingRevenue(Request $request)
    {
        $query = Invoice::with(['parent', 'child', 'items', 'payments']);

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

        // Filter by Date Range (invoice_date)
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }

        // Filter by Status
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by Type (daycare / therapy / mixed)
        $type = $request->input('type');
        if ($type === 'therapy') {
            $query->whereHas('items', fn($q) => $q->whereNotNull('therapy_session_id'))
                  ->whereDoesntHave('items', fn($q) => $q->whereNull('therapy_session_id'));
        } elseif ($type === 'daycare') {
            $query->whereDoesntHave('items', fn($q) => $q->whereNotNull('therapy_session_id'));
        } elseif ($type === 'mixed') {
            $query->whereHas('items', fn($q) => $q->whereNotNull('therapy_session_id'))
                  ->whereHas('items', fn($q) => $q->whereNull('therapy_session_id'));
        }

        // Export to CSV if requested
        if ($request->query('export') === 'csv') {
            return $this->exportBillingRevenueCsv($query);
        }

        // Calculate summary row metrics across the entire filtered dataset
        $summaryInvoices = (clone $query)->get();
        $nonCancelledInvoices = $summaryInvoices->where('status', '!=', 'cancelled');

        $totalInvoiced = (float) $nonCancelledInvoices->sum('total_amount');
        $totalCollected = (float) $summaryInvoices->sum(function ($inv) {
            return (float) $inv->payments->sum('paid_amount');
        });
        $totalOutstanding = max(0, $totalInvoiced - $totalCollected);
        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0.0;

        $summary = [
            'total_invoiced'    => $totalInvoiced,
            'total_collected'   => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'collection_rate'   => $collectionRate,
            'total_count'       => $summaryInvoices->count(),
            'paid_count'        => $summaryInvoices->where('status', 'paid')->count(),
            'overdue_count'     => $summaryInvoices->where('status', 'overdue')->count(),
            'draft_count'       => $summaryInvoices->where('status', 'draft')->count(),
            'cancelled_count'   => $summaryInvoices->where('status', 'cancelled')->count(),
        ];

        $invoices = $query->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.reports.billing-revenue', compact(
            'invoices',
            'summary',
            'fromDate',
            'toDate',
            'type',
            'status'
        ));
    }

    /**
     * Export billing / revenue report to CSV.
     */
    protected function exportBillingRevenueCsv($query)
    {
        $invoices = (clone $query)->orderBy('invoice_date', 'desc')->get();
        $filename = 'billing-revenue-report-' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($invoices) {
            $output = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($output, [
                'Invoice #',
                'Family',
                'Child',
                'Type',
                'Invoice Date',
                'Due Date',
                'Amount',
                'Paid Total',
                'Status',
                'Paid Date',
            ]);

            foreach ($invoices as $invoice) {
                $paidDate = $invoice->paid_date ? $invoice->paid_date->format('Y-m-d') : '';
                fputcsv($output, [
                    $invoice->invoice_number,
                    $invoice->parent->full_name ?? '—',
                    $invoice->child->full_name ?? '—',
                    $invoice->invoice_type_label,
                    $invoice->invoice_date->format('Y-m-d'),
                    $invoice->due_date->format('Y-m-d'),
                    number_format($invoice->total_amount, 2, '.', ''),
                    number_format($invoice->paid_total, 2, '.', ''),
                    $invoice->status_label,
                    $paidDate,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
