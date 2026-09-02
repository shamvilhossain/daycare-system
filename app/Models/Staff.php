<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $guarded = [];

    public function user()                { return $this->belongsTo(User::class); }
    public function activityOccurrences() { return $this->hasMany(ActivityOccurrence::class); }
    public function childDailyLogs()      { return $this->hasMany(ChildDailyLog::class); }
    public function announcements()       { return $this->hasMany(Announcement::class); }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
