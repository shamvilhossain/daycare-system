<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function occurrences() { return $this->hasMany(ActivityOccurrence::class); }
}
