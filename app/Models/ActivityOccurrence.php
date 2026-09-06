<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ActivityOccurrence extends Model
{
    protected $guarded = [];

    protected $casts = [
        'occurrence_date' => 'date',
    ];

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                        */
    /* ------------------------------------------------------------------ */

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function childDailyLogs()
    {
        return $this->hasMany(ChildDailyLog::class);
    }

    public function media()
    {
        return $this->hasMany(ActivityMedia::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeForDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('occurrence_date', $date);
        }
        return $query;
    }

    public function scopeForProgram($query, $programId)
    {
        if ($programId) {
            return $query->where('program_id', $programId);
        }
        return $query;
    }

    public function scopeForStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeForActivity($query, $activityId)
    {
        if ($activityId) {
            return $query->where('activity_id', $activityId);
        }
        return $query;
    }

    public function scopeForStaff($query, $staffId)
    {
        if ($staffId) {
            return $query->where('staff_id', $staffId);
        }
        return $query;
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                           */
    /* ------------------------------------------------------------------ */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planned'   => 'Planned',
            'completed' => 'Completed',
            'partial'   => 'Partial',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status ?? ''),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'planned'   => 'badge-info',
            'completed' => 'badge-success',
            'partial'   => 'badge-warning',
            'cancelled' => 'badge-danger',
            default     => 'badge-secondary',
        };
    }

    public function getFormattedStartTimeAttribute(): string
    {
        if (!$this->start_time) {
            return '—';
        }
        try {
            return Carbon::parse($this->start_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->start_time;
        }
    }

    public function getFormattedEndTimeAttribute(): string
    {
        if (!$this->end_time) {
            return '—';
        }
        try {
            return Carbon::parse($this->end_time)->format('g:i A');
        } catch (\Exception $e) {
            return $this->end_time;
        }
    }

    public function getTimeRangeAttribute(): string
    {
        if (!$this->start_time) {
            return 'Flexible time';
        }
        return $this->formatted_start_time . ($this->end_time ? ' - ' . $this->formatted_end_time : '');
    }
}
