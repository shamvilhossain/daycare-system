<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityMedia extends Model
{
    protected $table = 'activity_media';

    public function activityOccurrence() { return $this->belongsTo(ActivityOccurrence::class); }
}
