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

    /**
     * Total amount paid against this invoice.
     */
    public function getPaidTotalAttribute(): float
    {
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
