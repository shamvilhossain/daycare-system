<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public function child() { return $this->belongsTo(Child::class); }
}
