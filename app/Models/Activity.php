<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active'        => 'boolean',
        'duration_minutes' => 'integer',
    ];

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                        */
    /* ------------------------------------------------------------------ */

    public function occurrences()
    {
        return $this->hasMany(ActivityOccurrence::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                              */
    /* ------------------------------------------------------------------ */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /* ------------------------------------------------------------------ */
    /*  Accessors                                                           */
    /* ------------------------------------------------------------------ */

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'art'          => 'Art',
            'music'        => 'Music',
            'outdoor'      => 'Outdoor',
            'reading'      => 'Reading',
            'motor_skills' => 'Motor Skills',
            'sensory'      => 'Sensory',
            'cognitive'    => 'Cognitive',
            'social'       => 'Social',
            'language'     => 'Language',
            'math'         => 'Math',
            'science'      => 'Science',
            default        => 'Other',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'art'          => '#ef4444',
            'music'        => '#f59e0b',
            'outdoor'      => '#10b981',
            'reading'      => '#6366f1',
            'motor_skills' => '#a855f7',
            'sensory'      => '#06b6d4',
            'cognitive'    => '#3b82f6',
            'social'       => '#ec4899',
            'science'      => '#14b8a6',
            'math'         => '#8b5cf6',
            default        => '#6b7280',
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            'art'          => 'bg-danger-subtle text-danger border-danger-subtle',
            'music'        => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
            'outdoor'      => 'bg-success-subtle text-success border-success-subtle',
            'reading'      => 'bg-primary-subtle text-primary border-primary-subtle',
            'motor_skills' => 'bg-purple-subtle text-purple border-purple-subtle',
            'sensory'      => 'bg-info-subtle text-info-emphasis border-info-subtle',
            'cognitive'    => 'bg-blue-subtle text-primary border-blue-subtle',
            'science'      => 'bg-teal-subtle text-teal border-teal-subtle',
            default        => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        };
    }

    public function getDurationLabelAttribute(): string
    {
        if (!$this->duration_minutes) {
            return '—';
        }
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;
        return $h ? "{$h}h {$m}m" : "{$m}m";
    }
}
