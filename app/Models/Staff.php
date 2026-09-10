<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hire_date'     => 'date',
            'is_active'     => 'boolean',
        ];
    }

    public function user()                { return $this->belongsTo(User::class); }
    public function activityOccurrences() { return $this->hasMany(ActivityOccurrence::class); }
    public function childDailyLogs()      { return $this->hasMany(ChildDailyLog::class); }
    public function announcements()       { return $this->hasMany(Announcement::class); }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string
    {
        $first = mb_substr($this->first_name ?? '', 0, 1);
        $last  = mb_substr($this->last_name ?? '', 0, 1);
        return strtoupper("{$first}{$last}") ?: 'ST';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'teacher'   => 'Teacher',
            'assistant' => 'Assistant',
            'therapist' => 'Therapist',
            'admin'     => 'Admin',
            default     => ucfirst($this->role ?? ''),
        };
    }

    public function getDepartmentLabelAttribute(): string
    {
        return match ($this->department) {
            'therapy' => 'Therapy',
            default   => 'Daycare',
        };
    }

    public function getSpecializationLabelAttribute(): ?string
    {
        return match ($this->specialization) {
            'slt'   => 'Speech & Language Therapy (SLT)',
            'aba'   => 'Applied Behavior Analysis (ABA)',
            'ot'    => 'Occupational Therapy (OT)',
            default => null,
        };
    }

    public function getSpecializationShortAttribute(): ?string
    {
        return match ($this->specialization) {
            'slt'   => 'SLT',
            'aba'   => 'ABA',
            'ot'    => 'OT',
            default => null,
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('nid', 'like', "%{$term}%")
              ->orWhere('note', 'like', "%{$term}%")
              ->orWhereHas('user', function ($uq) use ($term) {
                  $uq->where('email', 'like', "%{$term}%");
              });
        });
    }

    public function scopeRole($query, ?string $role)
    {
        if ($role) {
            return $query->where('role', $role);
        }
        return $query;
    }

    public function scopeDepartment($query, ?string $dept)
    {
        if ($dept) {
            return $query->where('department', $dept);
        }
        return $query;
    }

    public function scopeSpecialization($query, ?string $spec)
    {
        if ($spec) {
            return $query->where('specialization', $spec);
        }
        return $query;
    }

    public function scopeActive($query, $isActive = true)
    {
        if ($isActive !== null && $isActive !== '') {
            return $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN));
        }
        return $query;
    }
}
