<?php

namespace App\Services;

use App\Models\ChildTherapyPackage;
use App\Models\TherapySession;
use App\Models\Staff;
use App\Models\TherapyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
     * Packages of this child that can pay for a session of this service on this date
     * (oldest first). Each entry carries how many sessions are still free to book.
     */
    public function packageOptions(int $childId, int $serviceId, string $date, ?int $excludeSessionId = null): array
    {
        return ChildTherapyPackage::usableFor($childId, $serviceId, $date)
            ->with('package')
            ->get()
            ->map(fn (ChildTherapyPackage $cp) => [
                'id'           => $cp->id,
                'name'         => $cp->package->name ?? 'Package #' . $cp->id,
                'purchased_at' => $cp->purchased_at->format('d M Y'),
                'expires_at'   => $cp->expires_at?->format('d M Y'),
                'included'     => $cp->includedFor($serviceId),
                'available'    => $cp->availableFor($serviceId, $excludeSessionId),
            ])
            ->filter(fn ($o) => $o['available'] > 0)
            ->values()
            ->all();
    }

    /**
     * Turn the form's "Payment" choice into a child_therapy_package_id.
     * '' / null = pay per session (billed on an invoice), 'auto' = oldest package with balance (FIFO),
     * a number = that package. Throws a validation error when the package can't pay for the session.
     */
    public function resolvePackage(?string $choice, array $data, ?TherapySession $session = null): ?int
    {
        if ($choice === null || $choice === '') {
            return null;
        }

        // A session already billed on an invoice can't be moved onto a package (it would be paid twice)
        if ($session && $session->invoiceItem()->exists() && (int) $session->child_therapy_package_id !== (int) $choice) {
            throw ValidationException::withMessages([
                'child_therapy_package_id' => 'This session is already billed on an invoice, so it cannot be paid from a package.',
            ]);
        }

        // Editing a session without changing its package / child / therapy keeps the existing link as-is
        if ($session && $session->child_therapy_package_id && (int) $choice === (int) $session->child_therapy_package_id
            && (int) $session->child_id === (int) $data['child_id']
            && (int) $session->therapy_service_id === (int) $data['therapy_service_id']) {
            return (int) $choice;
        }

        $options = collect($this->packageOptions(
            (int) $data['child_id'], (int) $data['therapy_service_id'], $data['session_date'], $session?->id
        ));

        if ($choice === 'auto') {
            $first = $options->first();
            if (!$first) {
                throw ValidationException::withMessages([
                    'child_therapy_package_id' => 'This child has no active package with sessions left for this therapy on that date. Choose "Pay per session" or purchase a package.',
                ]);
            }
            return $first['id'];
        }

        if (!$options->firstWhere('id', (int) $choice)) {
            throw ValidationException::withMessages([
                'child_therapy_package_id' => 'The selected package cannot be used: it must belong to this child, be active and not expired on the session date, include this therapy and have sessions left.',
            ]);
        }

        return (int) $choice;
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
