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
    ];

    public function parents()
    {
        return $this->belongsToMany(ParentProfile::class, 'parent_child', 'child_id', 'parent_id')
                    ->withPivot('relationship', 'is_primary', 'can_pickup')
                    ->withTimestamps();
    }

    public function enrollments()    { return $this->hasMany(Enrollment::class); }
    public function attendances()    { return $this->hasMany(Attendance::class); }
    public function dailyLogs()      { return $this->hasMany(ChildDailyLog::class); }
    public function documents()      { return $this->hasMany(Document::class); }

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
}
