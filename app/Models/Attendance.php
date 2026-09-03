<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $guarded = [];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForProgram($query, $programId)
    {
        if ($programId) {
            return $query->where('program_id', $programId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    // ── Accessors & Helpers ─────────────────────────────────────────────

    public function getIsCheckedInAttribute(): bool
    {
        return !empty($this->check_in_time) && empty($this->check_out_time) && in_array($this->status, ['present', 'late']);
    }

    public function getIsCheckedOutAttribute(): bool
    {
        return !empty($this->check_in_time) && !empty($this->check_out_time);
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        try {
            $in = Carbon::parse($this->check_in_time);
            $out = Carbon::parse($this->check_out_time);
            return max(0, $in->diffInMinutes($out));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes;
        if ($minutes === null) {
            if ($this->is_checked_in) {
                // Currently in daycare
                try {
                    $in = Carbon::parse($this->check_in_time);
                    $now = Carbon::now();
                    $mins = max(0, $in->diffInMinutes($now));
                    $hours = intdiv($mins, 60);
                    $remMins = $mins % 60;
                    return $hours > 0 ? "{$hours}h {$remMins}m (ongoing)" : "{$remMins}m (ongoing)";
                } catch (\Exception $e) {
                    return 'Ongoing';
                }
            }
            return '—';
        }

        $hours = intdiv($minutes, 60);
        $remMins = $minutes % 60;

        if ($hours > 0 && $remMins > 0) {
            return "{$hours}h {$remMins}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }
        return "{$remMins}m";
    }

    public function getFormattedCheckInTimeAttribute(): ?string
    {
        if (!$this->check_in_time) {
            return null;
        }
        try {
            return Carbon::parse($this->check_in_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->check_in_time;
        }
    }

    public function getFormattedCheckOutTimeAttribute(): ?string
    {
        if (!$this->check_out_time) {
            return null;
        }
        try {
            return Carbon::parse($this->check_out_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->check_out_time;
        }
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'present' => 'bg-success',
            'late'    => 'bg-warning text-dark',
            'absent'  => 'bg-danger',
            'excused' => 'bg-info text-dark',
            default   => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'Unknown');
    }
}
