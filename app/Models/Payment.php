<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payable_amount'  => 'decimal:2',
        'penalty_amount'  => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'paid_at'         => 'datetime',
    ];

    public function invoice()   { return $this->belongsTo(Invoice::class); }
    public function parent()    { return $this->belongsTo(ParentProfile::class, 'parent_id'); }
    public function child()     { return $this->belongsTo(Child::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }

    /**
     * Human-readable label for payment method.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'          => 'Cash',
            'card'          => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'online'        => 'Online',
            default         => ucfirst($this->payment_method ?? 'Unknown'),
        };
    }
}
