<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildTherapyPackageItem extends Model
{
    protected $guarded = [];

    public function childPackage() { return $this->belongsTo(ChildTherapyPackage::class, 'child_therapy_package_id'); }
    public function service()      { return $this->belongsTo(TherapyService::class, 'therapy_service_id'); }
    
}
