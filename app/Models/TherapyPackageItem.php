<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyPackageItem extends Model
{
    protected $guarded = [];

    public function package() { 
        return $this->belongsTo(TherapyPackage::class, 'therapy_package_id'); 
    }
    
    public function service() { 
        return $this->belongsTo(TherapyService::class, 'therapy_service_id'); 
    }
}
