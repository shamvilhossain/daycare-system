<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    protected $guarded = [];

    protected $hidden = ['store_password'];

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public function transactions()
    {
        return $this->hasMany(PaymentOnlineTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function endpoint(string $column): string
    {
        return rtrim($this->base_url, '/') . '/' . ltrim($this->{$column}, '/');
    }
}
