<?php

namespace App\Models;

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
}
