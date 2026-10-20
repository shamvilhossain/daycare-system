<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountDeletionRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pending',
            'completed' => 'Completed',
            'rejected'  => 'Rejected',
            default     => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'completed' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'rejected'  => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default     => 'bg-secondary text-white',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return $this->source === 'app' ? 'Parent app' : 'Web form';
    }
}
