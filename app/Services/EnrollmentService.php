<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function __construct(protected AdmissionFeeService $admissionFees)
    {
    }

    /**
     * Validate all business rules for an enrollment.
     *
     * @throws ValidationException
     */
    public function validateEligibility(
        Child $child,
        Program $program,
        string $startDate,
        ?string $status = 'pending',
        ?int $excludeEnrollmentId = null
    ): void {
        // 1. Program active check
        if (!$program->is_active) {
            throw ValidationException::withMessages([
                'program_id' => "The selected program '{$program->name}' is currently inactive.",
            ]);
        }

        // 2. Child active check
        if (!$child->is_active) {
            throw ValidationException::withMessages([
                'child_id' => "Child {$child->full_name} is currently marked as inactive.",
            ]);
        }

        // 3. Age eligibility check
        $parsedStartDate = Carbon::parse($startDate);
        $childAgeMonths = $child->ageInMonths($parsedStartDate);

        if ($program->min_age_months !== null && $childAgeMonths < $program->min_age_months) {
            throw ValidationException::withMessages([
                'child_id' => "Child {$child->full_name} is too young for {$program->name}. Age at start date: {$childAgeMonths} months (Minimum required: {$program->min_age_months} months).",
            ]);
        }

        if ($program->max_age_months !== null && $childAgeMonths > $program->max_age_months) {
            throw ValidationException::withMessages([
                'child_id' => "Child {$child->full_name} exceeds the maximum age for {$program->name}. Age at start date: {$childAgeMonths} months (Maximum allowed: {$program->max_age_months} months).",
            ]);
        }

        // 4. Capacity check (only for active or pending statuses)
        if (in_array($status, ['active', 'pending'])) {
            $this->checkCapacity($program, $excludeEnrollmentId);
        }

        // 5. Overlapping / Duplicate active enrollment in the same program
        $duplicateQuery = Enrollment::where('child_id', $child->id)
            ->where('program_id', $program->id)
            ->whereIn('status', ['active', 'pending']);

        if ($excludeEnrollmentId) {
            $duplicateQuery->where('id', '!=', $excludeEnrollmentId);
        }

        $duplicate = $duplicateQuery->first();
        if ($duplicate) {
            throw ValidationException::withMessages([
                'child_id' => "Child {$child->full_name} already has an active/pending enrollment (#{$duplicate->id}) in {$program->name}.",
            ]);
        }
    }

    /**
     * Check if program has remaining capacity.
     *
     * @throws ValidationException
     */
    public function checkCapacity(Program $program, ?int $excludeEnrollmentId = null): void
    {
        $activeCountQuery = $program->enrollments()
            ->whereIn('status', ['active', 'pending']);

        if ($excludeEnrollmentId) {
            $activeCountQuery->where('id', '!=', $excludeEnrollmentId);
        }

        $activeCount = $activeCountQuery->count();

        if ($activeCount >= $program->capacity) {
            throw ValidationException::withMessages([
                'program_id' => "Program '{$program->name}' has reached maximum capacity ({$activeCount}/{$program->capacity} enrolled).",
            ]);
        }
    }

    /**
     * Create a new enrollment applying business rules.
     */
    public function createEnrollment(array $data, User $creator): Enrollment
    {
        $child = Child::with('parents')->findOrFail($data['child_id']);
        $program = Program::findOrFail($data['program_id']);
        $status = $data['status'] ?? 'pending';

        $this->validateEligibility(
            $child,
            $program,
            $data['start_date'],
            $status
        );
        $this->admissionFees->assertBillable($child, $status);

        return DB::transaction(function () use ($data, $creator, $status) {
            $enrollment = $this->insertEnrollment($data, $creator, $status);

            // First enrolment ever → admission invoice (admission fee + first month); available as $enrollment->admissionInvoice
            $invoice = $this->admissionFees->invoiceForEnrollment(
                $enrollment, $creator, (bool) ($data['waive_admission_fee'] ?? false), $data['admission_waiver_reason'] ?? null
            );
            $enrollment->setRelation('admissionInvoice', $invoice);

            return $enrollment;
        });
    }

    protected function insertEnrollment(array $data, User $creator, string $status): Enrollment
    {
        $approvedAt = null;
        $approvedBy = null;

        if ($status === 'active') {
            $approvedAt = now();
            $approvedBy = $creator->id;
        }

        return Enrollment::create([
            'child_id'     => $data['child_id'],
            'program_id'   => $data['program_id'],
            'service_type' => $data['service_type'],
            'status'       => $status,
            'start_date'   => $data['start_date'],
            'end_date'     => $data['end_date'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'created_by'   => $creator->id,
            'approved_at'  => $approvedAt,
            'approved_by'  => $approvedBy,
        ]);
    }

    /**
     * Update an existing enrollment applying business rules.
     */
    public function updateEnrollment(Enrollment $enrollment, array $data, ?User $user = null): Enrollment
    {
        $child = Child::findOrFail($data['child_id']);
        $program = Program::findOrFail($data['program_id']);
        $newStatus = $data['status'] ?? $enrollment->status;

        $this->validateEligibility(
            $child,
            $program,
            $data['start_date'],
            $newStatus,
            $enrollment->id
        );

        $wasLive = in_array($enrollment->status, ['pending', 'active'], true);
        $isLive = in_array($newStatus, ['pending', 'active'], true);
        if (!$wasLive && $isLive) {
            $this->admissionFees->assertBillable($child->load('parents'), $newStatus);
        }

        return DB::transaction(function () use ($enrollment, $data, $user, $newStatus, $wasLive, $isLive) {
            $approvedAt = $enrollment->approved_at;
            $approvedBy = $enrollment->approved_by;

            if ($newStatus === 'active' && $enrollment->status !== 'active') {
                $approvedAt = now();
                $approvedBy = $user ? $user->id : $enrollment->approved_by;
            } elseif (in_array($newStatus, ['rejected', 'withdrawn'])) {
                if ($newStatus === 'withdrawn' && empty($data['end_date'])) {
                    $data['end_date'] = now()->toDateString();
                }
            }

            $enrollment->update([
                'child_id'     => $data['child_id'],
                'program_id'   => $data['program_id'],
                'service_type' => $data['service_type'],
                'status'       => $newStatus,
                'start_date'   => $data['start_date'],
                'end_date'     => $data['end_date'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'approved_at'  => $approvedAt,
                'approved_by'  => $approvedBy,
            ]);

            // Admission invoice follows the enrolment: raised when it (re)becomes live, released if rejected unpaid
            if ($newStatus === 'rejected' && $wasLive) {
                $this->admissionFees->releaseForEnrollment($enrollment);
            } elseif (!$wasLive && $isLive && $user) {
                $enrollment->setRelation('admissionInvoice', $this->admissionFees->invoiceForEnrollment($enrollment->fresh(), $user));
            }

            return $enrollment;
        });
    }

    /**
     * Approve a pending enrollment.
     */
    public function approve(Enrollment $enrollment, User $approver): Enrollment
    {
        if ($enrollment->status === 'active') {
            return $enrollment;
        }

        // Check program capacity before approving
        $this->checkCapacity($enrollment->program, $enrollment->id);

        $enrollment->update([
            'status'      => 'active',
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);

        return $enrollment;
    }

    /**
     * Reject a pending enrollment.
     */
    public function reject(Enrollment $enrollment, User $rejector, ?string $reason = null): Enrollment
    {
        $notes = $enrollment->notes;
        if ($reason) {
            $notes = ($notes ? $notes . "\n" : '') . "[Rejected by {$rejector->name} on " . now()->format('Y-m-d H:i') . "]: {$reason}";
        }

        DB::transaction(function () use ($enrollment, $notes) {
            $enrollment->update([
                'status'      => 'rejected',
                'notes'       => $notes,
                'approved_at' => null,
                'approved_by' => null,
            ]);

            // Unpaid admission invoice is cancelled so the fee is charged on a future enrolment
            $enrollment->setRelation('admissionInvoice', $this->admissionFees->releaseForEnrollment($enrollment));
        });

        return $enrollment;
    }

    /**
     * Delete an enrollment; an unpaid admission invoice raised for it is cancelled.
     */
    public function delete(Enrollment $enrollment): ?\App\Models\Invoice
    {
        return DB::transaction(function () use ($enrollment) {
            $released = $this->admissionFees->releaseForEnrollment($enrollment);
            $enrollment->delete();

            return $released;
        });
    }

    /**
     * Mark enrollment as withdrawn.
     */
    public function withdraw(Enrollment $enrollment, ?string $endDate = null): Enrollment
    {
        $enrollment->update([
            'status'   => 'withdrawn',
            'end_date' => $endDate ?? now()->toDateString(),
        ]);

        return $enrollment;
    }

    /**
     * Mark enrollment as graduated.
     */
    public function graduate(Enrollment $enrollment, ?string $endDate = null): Enrollment
    {
        $enrollment->update([
            'status'   => 'graduated',
            'end_date' => $endDate ?? now()->toDateString(),
        ]);

        return $enrollment;
    }
}
