<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityMedia extends Model
{
    protected $table   = 'activity_media';
    protected $guarded = [];

    /* ------------------------------------------------------------------ */
    /*  Relationships                                                        */
    /* ------------------------------------------------------------------ */

    public function activityOccurrence()
    {
        return $this->belongsTo(ActivityOccurrence::class);
    }
}
