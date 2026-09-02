<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'approved_at' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Human-readable label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pending Approval',
            'active'    => 'Active',
            'withdrawn' => 'Withdrawn',
            'graduated' => 'Graduated',
            'rejected'  => 'Rejected',
            default     => ucfirst($this->status ?? 'Unknown'),
        };
    }

    /**
     * Bootstrap badge styling class based on status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'active'    => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'withdrawn' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            'graduated' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'rejected'  => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            default     => 'bg-secondary text-white',
        };
    }

    /**
     * Human-readable label for service type.
     */
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

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }
}
