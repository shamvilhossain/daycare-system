<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    public function parentProfile()  { return $this->belongsTo(ParentProfile::class, 'parent_id'); }
    public function enrollments()    { return $this->hasMany(Enrollment::class); }
    public function attendances()    { return $this->hasMany(Attendance::class); }
    public function dailyLogs()      { return $this->hasMany(ChildDailyLog::class); }
    public function documents()      { return $this->hasMany(Document::class); }
    //public function guardians()      { return $this->belongsToMany(User::class, 'guardian_child')->withPivot('relationship', 'is_primary', 'can_pickup', 'access_level')->wherePivotNotNull('accepted_at'); }
    
}
