<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\ChildDailyLog;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChildDailyLogService
{
    /**
     * Retrieve the comprehensive merged daily log data for a specific child on a specific date.
     */
    public function getMergedDailyLogForChild(Child $child, string $date): array
    {
        // 1. Fetch attendance record for the date
        $attendance = Attendance::with('program')
            ->where('child_id', $child->id)
            ->whereDate('attendance_date', $date)
            ->first();

        // 2. Fetch all daily logs sorted chronologically
        $logs = ChildDailyLog::with(['staff.user', 'activityOccurrence.activity'])
            ->where('child_id', $child->id)
            ->whereDate('log_date', $date)
            ->chronological()
            ->get();

        // 3. Aggregate daily routine metrics
        $totalNapMinutes = 0;
        $napCount = 0;
        $mealsCount = 0;
        $activitiesCount = 0;
        $activitiesCompletedCount = 0;
        $diaperChangesCount = 0;
        $incidentsCount = 0;
        $medicationsCount = 0;
        $mealDetails = [];

        foreach ($logs as $log) {
            switch ($log->log_type) {
                case 'nap':
                    $napCount++;
                    if ($log->duration_minutes !== null) {
                        $totalNapMinutes += $log->duration_minutes;
                    }
                    break;

                case 'meal':
                case 'bottle':
                    $mealsCount++;
                    $mealDetails[] = [
                        'type'         => $log->meal_type ?? $log->log_type,
                        'items_served' => $log->items_served,
                        'amount_eaten' => $log->amount_eaten,
                        'quality'      => $log->quality,
                        'time'         => $log->formatted_start_time,
                    ];
                    break;

                case 'activity':
                    $activitiesCount++;
                    if ($log->is_completed) {
                        $activitiesCompletedCount++;
                    }
                    break;

                case 'diaper_change':
                    $diaperChangesCount++;
                    break;

                case 'incident':
                    $incidentsCount++;
                    break;

                case 'medication':
                    $medicationsCount++;
                    break;
            }
        }

        // Format total nap time
        $napHours = intdiv($totalNapMinutes, 60);
        $remMins = $totalNapMinutes % 60;
        $formattedSleepTotal = '0 min';
        if ($napHours > 0 && $remMins > 0) {
            $formattedSleepTotal = "{$napHours} hr {$remMins} min";
        } elseif ($napHours > 0) {
            $formattedSleepTotal = "{$napHours} hr" . ($napHours > 1 ? 's' : '');
        } elseif ($remMins > 0) {
            $formattedSleepTotal = "{$remMins} min";
        }

        $summary = [
            'total_logs'                 => $logs->count(),
            'total_nap_minutes'          => $totalNapMinutes,
            'formatted_sleep_total'      => $formattedSleepTotal,
            'nap_count'                  => $napCount,
            'meals_count'                => $mealsCount,
            'meal_details'               => $mealDetails,
            'activities_count'           => $activitiesCount,
            'activities_completed_count' => $activitiesCompletedCount,
            'activity_completion_rate'   => $activitiesCount > 0 ? round(($activitiesCompletedCount / $activitiesCount) * 100) : 0,
            'diaper_changes_count'       => $diaperChangesCount,
            'incidents_count'            => $incidentsCount,
            'medications_count'          => $medicationsCount,
        ];

        return [
            'child'      => $child,
            'date'       => $date,
            'attendance' => $attendance,
            'logs'       => $logs,
            'summary'    => $summary,
        ];
    }

    /**
     * Query paginated daily logs across all children with filters.
     */
    public function getPaginatedLogs(Request $request): LengthAwarePaginator
    {
        $query = ChildDailyLog::with(['child', 'staff.user', 'activityOccurrence.activity']);

        if ($date = $request->input('date')) {
            $query->whereDate('log_date', $date);
        }

        if ($childId = $request->input('child_id')) {
            $query->where('child_id', $childId);
        }

        if ($type = $request->input('log_type')) {
            $query->where('log_type', $type);
        }

        if ($staffId = $request->input('staff_id')) {
            $query->where('staff_id', $staffId);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('child', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('log_date', 'desc')
            ->chronological()
            ->paginate(15)
            ->appends($request->query());
    }

    /**
     * Create a new daily log entry.
     *
     * @throws ValidationException
     */
    public function createLog(array $data, User $actor): ChildDailyLog
    {
        // 1. Resolve staff_id
        $staffId = $data['staff_id'] ?? null;
        if (!$staffId) {
            if ($actor->staffProfile) {
                $staffId = $actor->staffProfile->id;
            } else {
                $firstStaff = Staff::first();
                if (!$firstStaff) {
                    throw ValidationException::withMessages([
                        'staff_id' => 'No staff profile found in the system to attribute this log to.',
                    ]);
                }
                $staffId = $firstStaff->id;
            }
        }
        $data['staff_id'] = $staffId;

        // 2. Clean fields according to log type
        $data = $this->sanitizeFieldsForType($data);

        return ChildDailyLog::create($data);
    }

    /**
     * Update an existing daily log entry.
     */
    public function updateLog(ChildDailyLog $log, array $data): ChildDailyLog
    {
        $data = $this->sanitizeFieldsForType($data);
        $log->update($data);
        return $log;
    }

    /**
     * Delete a daily log entry.
     */
    public function deleteLog(ChildDailyLog $log): bool
    {
        return $log->delete();
    }

    /**
     * Strip irrelevant columns according to log_type.
     */
    protected function sanitizeFieldsForType(array $data): array
    {
        $type = $data['log_type'] ?? null;

        if (!in_array($type, ['meal', 'bottle'])) {
            $data['meal_type'] = null;
            $data['items_served'] = null;
            $data['amount_eaten'] = null;
            $data['quality'] = null;
        }

        if ($type !== 'activity') {
            $data['activity_occurrence_id'] = null;
            if ($type !== 'nap') {
                $data['is_completed'] = null;
            }
        }

        return $data;
    }
}
