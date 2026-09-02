<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $table = 'parents';
    protected $guarded = [];

    public function user(){ 
        return $this->belongsTo(User::class); 
    }
    public function children(){ 
        return $this->hasMany(Child::class, 'parent_id'); 
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
