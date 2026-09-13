<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyService extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'session_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function sessions()
    {
        return $this->hasMany(TherapySession::class);
    }
}
