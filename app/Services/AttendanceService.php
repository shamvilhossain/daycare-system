<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * Get operational roster for a specific date and optional program.
     */
    public function getRoster(
        string $date,
        ?int $programId = null,
        ?string $search = null,
        ?string $statusFilter = null
    ): array {
        // 1. Fetch active enrollments
        $enrollmentQuery = Enrollment::with(['child.parents', 'program'])
            ->where('status', 'active')
            ->where('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $date);
            });

        if ($programId) {
            $enrollmentQuery->where('program_id', $programId);
        }

        if ($search) {
            $enrollmentQuery->whereHas('child', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $enrollments = $enrollmentQuery->get();

        // 2. Fetch all attendance records for this date (and program if given)
        $attendanceQuery = Attendance::with(['child.parents', 'program'])
            ->whereDate('attendance_date', $date);

        if ($programId) {
            $attendanceQuery->where('program_id', $programId);
        }

        if ($search) {
            $attendanceQuery->whereHas('child', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $attendances = $attendanceQuery->get()->keyBy(function ($item) {
            return "{$item->child_id}_{$item->program_id}";
        });

        // 3. Build unified roster items
        $roster = [];
        $seenKeys = [];

        foreach ($enrollments as $enrollment) {
            $key = "{$enrollment->child_id}_{$enrollment->program_id}";
            $seenKeys[$key] = true;

            $attendance = $attendances->get($key);
            $status = $attendance ? $attendance->status : 'not_recorded';

            if ($statusFilter && $status !== $statusFilter) {
                if (!($statusFilter === 'not_recorded' && !$attendance)) {
                    continue;
                }
            }

            $roster[] = [
                'child'          => $enrollment->child,
                'program'        => $enrollment->program,
                'enrollment'     => $enrollment,
                'attendance'     => $attendance,
                'status'         => $status,
                'is_checked_in'  => $attendance ? $attendance->is_checked_in : false,
                'is_checked_out' => $attendance ? $attendance->is_checked_out : false,
            ];
        }

        // Include any attendance records on that date that might not have an active enrollment record
        foreach ($attendances as $key => $attendance) {
            if (!isset($seenKeys[$key])) {
                $status = $attendance->status;
                if ($statusFilter && $status !== $statusFilter) {
                    continue;
                }

                $roster[] = [
                    'child'          => $attendance->child,
                    'program'        => $attendance->program,
                    'enrollment'     => null,
                    'attendance'     => $attendance,
                    'status'         => $status,
                    'is_checked_in'  => $attendance->is_checked_in,
                    'is_checked_out' => $attendance->is_checked_out,
                ];
            }
        }

        // Sort by child's first name
        usort($roster, function ($a, $b) {
            return strcasecmp($a['child']->first_name ?? '', $b['child']->first_name ?? '');
        });

        return $roster;
    }

    /**
     * Compute real-time attendance statistics for the date and program.
     */
    public function getStats(string $date, ?int $programId = null): array
    {
        $attendanceQuery = Attendance::whereDate('attendance_date', $date);
        if ($programId) {
            $attendanceQuery->where('program_id', $programId);
        }

        $records = $attendanceQuery->get();

        $enrolledQuery = Enrollment::where('status', 'active')
            ->where('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $date);
            });
        if ($programId) {
            $enrolledQuery->where('program_id', $programId);
        }
        $totalEnrolled = $enrolledQuery->count();

        $presentCount = 0;
        $checkedOutCount = 0;
        $lateCount = 0;
        $absentCount = 0;
        $excusedCount = 0;

        foreach ($records as $record) {
            if ($record->status === 'present') {
                if ($record->check_out_time) {
                    $checkedOutCount++;
                } else {
                    $presentCount++;
                }
            } elseif ($record->status === 'late') {
                if ($record->check_out_time) {
                    $checkedOutCount++;
                } else {
                    $lateCount++;
                }
            } elseif ($record->status === 'absent') {
                $absentCount++;
            } elseif ($record->status === 'excused') {
                $excusedCount++;
            }
        }

        $totalRecorded = $records->count();
        $notCheckedIn = max(0, $totalEnrolled - ($presentCount + $checkedOutCount + $lateCount + $absentCount + $excusedCount));

        return [
            'total_enrolled'  => $totalEnrolled,
            'currently_in'    => $presentCount + $lateCount, // currently on premises
            'checked_out'     => $checkedOutCount,
            'late'            => $lateCount,
            'absent'          => $absentCount,
            'excused'         => $excusedCount,
            'not_checked_in'  => $notCheckedIn,
            'total_recorded'  => $totalRecorded,
        ];
    }

    /**
     * Perform Check-In for a child.
     *
     * @throws ValidationException
     */
    public function checkIn(array $data, ?User $actor = null): Attendance
    {
        $childId = $data['child_id'];
        $programId = $data['program_id'];
        $date = $data['attendance_date'] ?? Carbon::today()->toDateString();
        $time = $data['check_in_time'] ?? Carbon::now()->format('H:i:s');
        $status = $data['status'] ?? 'present';
        $notes = $data['notes'] ?? null;

        $attendance = Attendance::firstOrNew([
            'child_id'        => $childId,
            'program_id'      => $programId,
            'attendance_date' => $date,
        ]);

        if ($attendance->exists && $attendance->check_in_time && !$attendance->check_out_time) {
            throw ValidationException::withMessages([
                'child_id' => "Child is already checked in at {$attendance->formatted_check_in_time}.",
            ]);
        }

        $attendance->check_in_time = $time;
        $attendance->check_out_time = null; // reset if re-checking in
        $attendance->status = $status;
        if ($notes !== null) {
            $attendance->notes = $notes;
        }

        $attendance->save();
        return $attendance;
    }

    /**
     * Perform Check-Out for an existing attendance record.
     *
     * @throws ValidationException
     */
    public function checkOut(Attendance $attendance, ?string $time = null, ?string $notes = null): Attendance
    {
        if (in_array($attendance->status, ['absent', 'excused'])) {
            throw ValidationException::withMessages([
                'status' => 'Cannot check out a child marked as absent or excused.',
            ]);
        }

        if (!$attendance->check_in_time) {
            throw ValidationException::withMessages([
                'check_in_time' => 'Child must be checked in before they can be checked out.',
            ]);
        }

        $checkOutTime = $time ?? Carbon::now()->format('H:i:s');

        if ($attendance->check_in_time >= $checkOutTime) {
            throw ValidationException::withMessages([
                'check_out_time' => "Check-out time ({$checkOutTime}) must be after check-in time ({$attendance->check_in_time}).",
            ]);
        }

        $attendance->check_out_time = $checkOutTime;
        if ($notes) {
            $attendance->notes = $attendance->notes ? ($attendance->notes . "\n" . $notes) : $notes;
        }

        $attendance->save();
        return $attendance;
    }

    /**
     * Mark a child as absent or excused.
     */
    public function markAbsent(int $childId, int $programId, string $date, ?string $notes = null, string $status = 'absent'): Attendance
    {
        $attendance = Attendance::firstOrNew([
            'child_id'        => $childId,
            'program_id'      => $programId,
            'attendance_date' => $date,
        ]);

        $attendance->status = in_array($status, ['absent', 'excused']) ? $status : 'absent';
        $attendance->check_in_time = null;
        $attendance->check_out_time = null;
        if ($notes !== null) {
            $attendance->notes = $notes;
        }

        $attendance->save();
        return $attendance;
    }

    /**
     * Bulk check-in multiple children into a program.
     */
    public function bulkCheckIn(array $childIds, int $programId, string $date, ?string $time = null, ?string $notes = null): int
    {
        $checkInTime = $time ?? Carbon::now()->format('H:i:s');
        $count = 0;

        DB::transaction(function () use ($childIds, $programId, $date, $checkInTime, $notes, &$count) {
            foreach ($childIds as $childId) {
                $attendance = Attendance::firstOrNew([
                    'child_id'        => $childId,
                    'program_id'      => $programId,
                    'attendance_date' => $date,
                ]);

                if (!$attendance->check_in_time || $attendance->check_out_time) {
                    $attendance->check_in_time = $checkInTime;
                    $attendance->check_out_time = null;
                    $attendance->status = 'present';
                    if ($notes) {
                        $attendance->notes = $notes;
                    }
                    $attendance->save();
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Bulk check-out attendance records.
     */
    public function bulkCheckOut(array $attendanceIds, ?string $time = null, ?string $notes = null): int
    {
        $checkOutTime = $time ?? Carbon::now()->format('H:i:s');
        $count = 0;

        DB::transaction(function () use ($attendanceIds, $checkOutTime, $notes, &$count) {
            $attendances = Attendance::whereIn('id', $attendanceIds)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->get();

            foreach ($attendances as $attendance) {
                if ($checkOutTime > $attendance->check_in_time) {
                    $attendance->check_out_time = $checkOutTime;
                    if ($notes) {
                        $attendance->notes = $attendance->notes ? ($attendance->notes . "\n" . $notes) : $notes;
                    }
                    $attendance->save();
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * Create or update attendance record from form submission.
     */
    public function createOrUpdate(array $data): Attendance
    {
        $attendance = Attendance::firstOrNew([
            'child_id'        => $data['child_id'],
            'program_id'      => $data['program_id'],
            'attendance_date' => $data['attendance_date'],
        ]);

        $attendance->status = $data['status'];
        $attendance->notes = $data['notes'] ?? null;

        if (in_array($data['status'], ['absent', 'excused'])) {
            $attendance->check_in_time = null;
            $attendance->check_out_time = null;
        } else {
            $attendance->check_in_time = $data['check_in_time'] ?? null;
            $attendance->check_out_time = $data['check_out_time'] ?? null;
        }

        $attendance->save();
        return $attendance;
    }
}
