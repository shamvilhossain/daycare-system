<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildFoundReport extends Model
{
    protected $guarded = [];
    public function safetyTag()
    {
        return $this->belongsTo(ChildSafetyTag::class, 'child_safety_tag_id');
    }
}
