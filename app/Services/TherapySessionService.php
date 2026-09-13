<?php

namespace App\Services;

use App\Models\TherapySession;
use App\Models\Staff;
use App\Models\TherapyService;
use Illuminate\Support\Facades\DB;

class TherapySessionService
{
    /**
     * Check if the therapist has an overlapping session on the given date/time.
     * Optionally exclude a session (for updates).
     */
    public function hasOverlap(int $staffId, string $sessionDate, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $query = TherapySession::where('staff_id', $staffId)
            ->where('session_date', $sessionDate)
            ->whereIn('status', ['scheduled', 'completed']) // ignore cancelled/no_show
            ->where(function ($q) use ($startTime, $endTime) {
                // Overlap: existing.start < new.end AND existing.end > new.start
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check that a therapist's specialization matches the therapy service's therapy_type.
     */
    public function specializationMatches(int $staffId, int $therapyServiceId): bool
    {
        $staff = Staff::findOrFail($staffId);
        $service = TherapyService::findOrFail($therapyServiceId);

        return $staff->specialization === $service->therapy_type;
    }

    /**
     * Create a new therapy session with validation.
     */
    public function createSession(array $data, $user): TherapySession
    {
        return DB::transaction(function () use ($data, $user) {
            $data['booked_by'] = $user->id;
            return TherapySession::create($data);
        });
    }

    /**
     * Update an existing therapy session.
     */
    public function updateSession(TherapySession $session, array $data): TherapySession
    {
        return DB::transaction(function () use ($session, $data) {
            $session->update($data);
            return $session->fresh();
        });
    }
}
