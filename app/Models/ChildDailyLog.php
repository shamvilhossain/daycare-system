<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildDailyLog extends Model
{
    public function child()               { return $this->belongsTo(Child::class); }
    public function staff()               { return $this->belongsTo(Staff::class); }
    public function activityOccurrence()  { return $this->belongsTo(ActivityOccurrence::class); }
}
