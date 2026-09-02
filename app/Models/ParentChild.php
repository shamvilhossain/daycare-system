<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentChild extends Model
{
    protected $table = 'parent_child';
    protected $guarded = [];

    public function parent() { return $this->belongsTo(ParentProfile::class, 'parent_id'); }
    public function child()  { return $this->belongsTo(Child::class); }
}
