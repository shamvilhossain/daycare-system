<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
    
    public function items()         { return $this->hasMany(TherapyPackageItem::class); }
    public function childPurchases(){ return $this->hasMany(ChildTherapyPackage::class); }

    public function getTotalSessionsAttribute(): int
    {
        return $this->items->sum('session_count'); // derived — never stored, never drifts
    }
}
