<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active'       => 'boolean',
        'min_age_months'  => 'integer',
        'max_age_months'  => 'integer',
        'capacity'        => 'integer',
        'monthly_fee'     => 'decimal:2',
        'daily_rate'      => 'decimal:2',
        'hourly_rate'     => 'decimal:2',
    ];

    public function enrollments()          { return $this->hasMany(Enrollment::class); }
    public function attendances()          { return $this->hasMany(Attendance::class); }
    public function activityOccurrences()  { return $this->hasMany(ActivityOccurrence::class); }

    public function activeEnrollments()
    {
        return $this->hasMany(Enrollment::class)->whereIn('status', ['active', 'pending']);
    }

    public function getActiveEnrollmentsCountAttribute(): int
    {
        return $this->enrollments()->whereIn('status', ['active', 'pending'])->count();
    }

    public function getAvailableCapacityAttribute(): int
    {
        return max(0, (int)$this->capacity - $this->active_enrollments_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->available_capacity <= 0;
    }

    public function getAgeRangeLabelAttribute(): string
    {
        if ($this->min_age_months !== null && $this->max_age_months !== null) {
            return "{$this->min_age_months} - {$this->max_age_months} months";
        } elseif ($this->min_age_months !== null) {
            return "From {$this->min_age_months} months";
        } elseif ($this->max_age_months !== null) {
            return "Up to {$this->max_age_months} months";
        }
        return "All ages";
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            'full_day'     => 'Full Day',
            'half_day'     => 'Half Day',
            'after_school' => 'After School',
            'drop_in'      => 'Drop-In',
            default        => ucwords(str_replace('_', ' ', $this->service_type ?? '')),
        };
    }
}
