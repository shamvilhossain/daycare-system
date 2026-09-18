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
    public function children()
    {
        return $this->belongsToMany(Child::class, 'parent_child', 'parent_id', 'child_id')
                     ->withPivot('relationship', 'is_primary', 'can_pickup')
                     ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'parent_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Alias for mobile column to ensure compatibility with phone references.
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['mobile'] ?? null;
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['mobile'] = $value;
    }

    /**
     * Helper to access parent's account email.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }
}
