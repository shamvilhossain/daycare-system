<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'ec_authorized_pickup' => 'boolean',
        'additional_contacts' => 'array',
        'admission_fee_waived_at' => 'datetime',
    ];

    /** Invoice raised on the child's first enrolment (admission fee + first month). */
    public function admissionInvoice()
    {
        return $this->belongsTo(Invoice::class, 'admission_invoice_id');
    }

    /** Admission fee already charged or waived — never charge it again. */
    public function getIsAdmittedAttribute(): bool
    {
        return $this->admission_invoice_id !== null || $this->admission_fee_waived_at !== null;
    }

    /**
     * Admission fee status: 'due' (not charged yet), 'waived', 'paid' or 'unpaid' (invoiced, not fully paid).
     */
    public function getAdmissionStatusAttribute(): string
    {
        if ($this->admission_fee_waived_at) {
            return 'waived';
        }
        if (!$this->admission_invoice_id) {
            return 'due';
        }
        return $this->admissionInvoice?->status === 'paid' ? 'paid' : 'unpaid';
    }

    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'parent_child', 'child_id', 'parent_id')
            ->withPivot('relationship', 'is_primary', 'can_pickup')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function dailyLogs()
    {
        return $this->hasMany(ChildDailyLog::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get age in months as of today or a given reference date.
     */
    public function ageInMonths(?Carbon $referenceDate = null): int
    {
        if (!$this->date_of_birth) {
            return 0;
        }
        $ref = $referenceDate ?? Carbon::today();
        return (int) $this->date_of_birth->diffInMonths($ref);
    }

    public function getAgeInMonthsAttribute(): int
    {
        return $this->ageInMonths();
    }

    public function getFormattedAgeAttribute(): string
    {
        if (!$this->date_of_birth) {
            return '—';
        }
        $diff = $this->date_of_birth->diff(Carbon::today());
        if ($diff->y > 0) {
            return $diff->y . ' yr' . ($diff->y > 1 ? 's' : '') . ($diff->m > 0 ? ' ' . $diff->m . ' mo' : '');
        }
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    }

    public function therapySessions()
    {
        return $this->hasMany(TherapySession::class);
    }

    public function safetyTags()
    {
        return $this->hasMany(ChildSafetyTag::class);
    }

    /**
     * Get the primary parent profile for this child.
     * Returns the parent marked as primary, or the first parent as fallback.
     */
    public function getParentProfileAttribute(): ?ParentProfile
    {
        $parents = $this->relationLoaded('parents') ? $this->parents : $this->parents()->get();

        return $parents->first(fn($p) => $p->pivot->is_primary) ?? $parents->first();
    }

    public function therapyPackages() { 
        return $this->hasMany(ChildTherapyPackage::class); 
    }
}
