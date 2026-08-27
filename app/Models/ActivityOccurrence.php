<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityOccurrence extends Model
{
    public function activity()       { return $this->belongsTo(Activity::class); }
    public function program()        { return $this->belongsTo(Program::class); }
    public function staff()          { return $this->belongsTo(Staff::class); }
    public function childDailyLogs() { return $this->hasMany(ChildDailyLog::class); }
    public function media()          { return $this->hasMany(ActivityMedia::class); }
}
