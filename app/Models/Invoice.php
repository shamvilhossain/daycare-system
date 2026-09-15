<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function parent()  { return $this->belongsTo(ParentProfile::class, 'parent_id'); }
    public function child()   { return $this->belongsTo(Child::class); }
    public function items()   { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function latestPayment() { return $this->hasOne(Payment::class)->latestOfMany('paid_at'); }

    /**
     * Total amount paid against this invoice.
     */
    public function getPaidTotalAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('paid_amount');
        }
        return (float) $this->payments()->sum('paid_amount');
    }

    /**
     * Remaining balance on this invoice.
     */
    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->paid_total);
    }

    /**
     * Get the latest payment date if paid.
     */
    public function getPaidDateAttribute()
    {
        if ($this->relationLoaded('payments')) {
            $latest = $this->payments->sortByDesc('paid_at')->first();
            return $latest ? ($latest->paid_at instanceof \Carbon\Carbon ? $latest->paid_at : \Carbon\Carbon::parse($latest->paid_at)) : null;
        }
        $latestPaidAt = $this->payments()->latest('paid_at')->value('paid_at');
        return $latestPaidAt ? \Carbon\Carbon::parse($latestPaidAt) : null;
    }

    /**
     * Determine invoice classification type: 'daycare', 'therapy', or 'mixed'.
     */
    public function getInvoiceTypeAttribute(): string
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        if ($items->isEmpty()) {
            return 'daycare';
        }

        $hasTherapy = $items->contains(fn($item) => !is_null($item->therapy_session_id));
        $hasDaycare = $items->contains(fn($item) => is_null($item->therapy_session_id));

        if ($hasTherapy && $hasDaycare) {
            return 'mixed';
        }
        if ($hasTherapy) {
            return 'therapy';
        }
        return 'daycare';
    }

    /**
     * Human-readable label for invoice type.
     */
    public function getInvoiceTypeLabelAttribute(): string
    {
        return match ($this->invoice_type) {
            'therapy' => 'Therapy',
            'mixed'   => 'Mixed',
            default   => 'Daycare',
        };
    }

    /**
     * Badge styling class for invoice type.
     */
    public function getInvoiceTypeBadgeClassAttribute(): string
    {
        return match ($this->invoice_type) {
            'therapy' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'mixed'   => 'bg-purple-subtle text-purple-emphasis border border-purple-subtle',
            default   => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        };
    }

    /**
     * Human-readable label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'paid'      => 'Paid',
            'overdue'   => 'Overdue',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status ?? 'Unknown'),
        };
    }

    /**
     * Bootstrap badge styling class based on status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'paid'      => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'overdue'   => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            'cancelled' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default     => 'bg-secondary text-white',
        };
    }
}
