<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    /** All values of invoices.status, in lifecycle order. */
    public const STATUSES = ['draft', 'sent', 'partially_paid', 'paid', 'overdue', 'cancelled'];

    /** Statuses that still accept payments. */
    public const OPEN_STATUSES = ['draft', 'sent', 'partially_paid', 'overdue'];

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
    public function onlineTransactions() { return $this->hasMany(PaymentOnlineTransaction::class); }

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

        // Therapy = a billed session or a therapy package bought on this invoice
        $isTherapy = fn ($item) => !is_null($item->therapy_session_id) || !is_null($item->child_therapy_package_id);
        $hasTherapy = $items->contains($isTherapy);
        $hasDaycare = $items->contains(fn ($item) => !$isTherapy($item));

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
            'draft'          => 'Draft',
            'sent'           => 'Sent',
            'partially_paid' => 'Partially Paid',
            'paid'           => 'Paid',
            'overdue'        => 'Overdue',
            'cancelled'      => 'Cancelled',
            default          => ucfirst($this->status ?? 'Unknown'),
        };
    }

    /**
     * Bootstrap badge styling class based on status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft'          => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'sent'           => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            'partially_paid' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            'paid'           => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'overdue'        => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            'cancelled'      => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default          => 'bg-secondary text-white',
        };
    }

    /**
     * Whether the invoice can still receive payments (not paid, not cancelled).
     */
    public function getIsOpenAttribute(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }
}
