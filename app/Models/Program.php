<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public function enrollments()          { return $this->hasMany(Enrollment::class); }
    public function attendances()          { return $this->hasMany(Attendance::class); }
    public function activityOccurrences()  { return $this->hasMany(ActivityOccurrence::class); }
}
