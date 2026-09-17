<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChildSafetyTag extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function foundReports()
    {
        return $this->hasMany(ChildFoundReport::class);
    }

    protected static function booted()
    {
        static::creating(fn($tag) => $tag->token ??= Str::random(32));
    }
}
