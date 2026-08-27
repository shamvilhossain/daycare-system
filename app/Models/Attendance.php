<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    public function child()   { return $this->belongsTo(Child::class); }
    public function program() { return $this->belongsTo(Program::class); }
}
