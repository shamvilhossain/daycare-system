<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ChildDailyLog extends Model
{
    protected $table = 'child_daily_logs';
    protected $guarded = [];

    protected $casts = [
        'log_date'     => 'date',
        'is_completed' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function activityOccurrence()
    {
        return $this->belongsTo(ActivityOccurrence::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('log_date', $date);
    }

    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('log_type', $type);
        }
        return $query;
    }

    public function scopeChronological($query)
    {
        return $query->orderByRaw("COALESCE(start_time, '00:00:00') ASC")->orderBy('created_at', 'asc');
    }

    // ── Accessors & Helpers ─────────────────────────────────────────────

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        try {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            return max(0, $start->diffInMinutes($end));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes;
        if ($minutes === null) {
            return '—';
        }

        $hours = intdiv($minutes, 60);
        $remMins = $minutes % 60;

        if ($hours > 0 && $remMins > 0) {
            return "{$hours} hr {$remMins} min";
        } elseif ($hours > 0) {
            return "{$hours} hr" . ($hours > 1 ? 's' : '');
        }
        return "{$remMins} min";
    }

    public function getFormattedStartTimeAttribute(): ?string
    {
        if (!$this->start_time) {
            return null;
        }
        try {
            return Carbon::parse($this->start_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->start_time;
        }
    }

    public function getFormattedEndTimeAttribute(): ?string
    {
        if (!$this->end_time) {
            return null;
        }
        try {
            return Carbon::parse($this->end_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->end_time;
        }
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->log_type) {
            'nap'             => 'Nap & Sleep',
            'meal'            => 'Meal & Feeding',
            'bottle'          => 'Bottle Feeding',
            'diaper_change'   => 'Diaper / Restroom',
            'activity'        => 'Learning Activity',
            'incident'        => 'Incident / Health Note',
            'special_program' => 'Special Program',
            'medication'      => 'Medication Administered',
            'other'           => 'General Note',
            default           => ucwords(str_replace('_', ' ', $this->log_type ?? '')),
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->log_type) {
            'nap'             => 'bi-moon-stars-fill',
            'meal'            => 'bi-egg-fried',
            'bottle'          => 'bi-cup-straw',
            'diaper_change'   => 'bi-droplet-half',
            'activity'        => 'bi-palette-fill',
            'incident'        => 'bi-exclamation-triangle-fill',
            'special_program' => 'bi-stars',
            'medication'      => 'bi-capsule',
            default           => 'bi-card-text',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->log_type) {
            'nap'             => 'bg-purple-subtle text-purple',
            'meal'            => 'bg-success-subtle text-success',
            'bottle'          => 'bg-info-subtle text-info',
            'diaper_change'   => 'bg-warning-subtle text-warning-emphasis',
            'activity'        => 'bg-primary-subtle text-primary',
            'incident'        => 'bg-danger-subtle text-danger',
            'special_program' => 'bg-secondary-subtle text-secondary',
            'medication'      => 'bg-danger-subtle text-danger',
            default           => 'bg-light text-dark',
        };
    }

    public function getQualityBadgeClassAttribute(): string
    {
        return match ($this->quality) {
            'good'    => 'bg-success',
            'fair'    => 'bg-info text-dark',
            'poor'    => 'bg-warning text-dark',
            'refused' => 'bg-danger',
            default   => 'bg-secondary',
        };
    }
}
