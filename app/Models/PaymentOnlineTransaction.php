<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentOnlineTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount'              => 'decimal:2',
        'pg_transaction_date' => 'datetime',
        'is_overdue'          => 'boolean',
    ];

    // Values of the `status` column (see migration comment).
    public const STATUS_INITIATED = 0;
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAILED = 2;
    public const STATUS_CANCELLED = 3;
    public const STATUS_CALLBACK_RECEIVED = 4; // validated but held (e.g. risky) — needs manual review
    public const STATUS_AMOUNT_MISMATCH = 5;
    public const STATUS_WRONG_STATUS = 6;

    public function channel()
    {
        return $this->belongsTo(PaymentChannel::class, 'payment_channel_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function getIsSuccessfulAttribute(): bool
    {
        return (int) $this->status === self::STATUS_SUCCESS;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_INITIATED         => 'Initiated',
            self::STATUS_SUCCESS           => 'Success',
            self::STATUS_FAILED            => 'Failed',
            self::STATUS_CANCELLED         => 'Cancelled',
            self::STATUS_CALLBACK_RECEIVED => 'On hold (review)',
            self::STATUS_AMOUNT_MISMATCH   => 'Amount mismatch',
            self::STATUS_WRONG_STATUS      => 'Invalid status',
            default                        => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_SUCCESS                               => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::STATUS_INITIATED                             => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::STATUS_CANCELLED                             => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            self::STATUS_CALLBACK_RECEIVED                     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            default                                            => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        };
    }

    /** Card / wallet the parent used, e.g. "BKASH-BKash" → "bKash". */
    public function getMethodLabelAttribute(): ?string
    {
        if (!$this->pg_card_type) {
            return null;
        }

        $label = str_contains($this->pg_card_type, '-') ? substr($this->pg_card_type, strpos($this->pg_card_type, '-') + 1) : $this->pg_card_type;

        return $label === 'BKash' ? 'bKash' : $label;
    }
}
