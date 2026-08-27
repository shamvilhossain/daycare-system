<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $table = 'parents';

    public function user(){ 
        return $this->belongsTo(User::class); 
    }
    public function children(){ 
        return $this->hasMany(Child::class, 'parent_id'); 
    }
}
