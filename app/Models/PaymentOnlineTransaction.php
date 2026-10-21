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

    /** Minutes after which an unfinished gateway session is treated as abandoned (display only). */
    public const ABANDON_AFTER_MINUTES = 60;

    public function getIsSuccessfulAttribute(): bool
    {
        return (int) $this->status === self::STATUS_SUCCESS;
    }

    /** Parent left the gateway without paying (no callback for a long time). */
    public function getIsAbandonedAttribute(): bool
    {
        return (int) $this->status === self::STATUS_INITIATED
            && $this->created_at
            && $this->created_at->lt(now()->subMinutes(self::ABANDON_AFTER_MINUTES));
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_abandoned) {
            return 'Abandoned';
        }

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
        if ($this->is_abandoned) {
            return 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle';
        }

        return match ((int) $this->status) {
            self::STATUS_SUCCESS                               => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            self::STATUS_INITIATED                             => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::STATUS_CANCELLED                             => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            self::STATUS_CALLBACK_RECEIVED                     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            default                                            => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        };
    }

    /**
     * Card / wallet the parent used.
     * SSLCommerz card_type is "<SCHEME>-<channel>": "VISA-Dutch Bangla" → "Visa · Dutch Bangla",
     * "BKASH-BKash" → "bKash", "NAGAD-Nagad" → "Nagad".
     */
    public function getMethodLabelAttribute(): ?string
    {
        if (!$this->pg_card_type) {
            return null;
        }

        [$scheme, $channel] = array_pad(explode('-', $this->pg_card_type, 2), 2, null);
        $cardSchemes = ['VISA' => 'Visa', 'MASTER' => 'Mastercard', 'MASTERCARD' => 'Mastercard', 'AMEX' => 'Amex', 'UNIONPAY' => 'UnionPay', 'NEXUS' => 'Nexus'];
        $schemeKey = strtoupper((string) ($this->pg_card_brand ?: $scheme));

        if (isset($cardSchemes[$schemeKey])) {
            return $cardSchemes[$schemeKey] . ($channel ? ' · ' . $channel : '');
        }

        $label = $channel ?: $scheme;

        return strcasecmp($label, 'bkash') === 0 ? 'bKash' : $label;
    }
}
