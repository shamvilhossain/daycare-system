<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get a human-readable label for the document type.
     */
    public function getDocTypeLabelAttribute(): string
    {
        return match ($this->doc_type) {
            'birth_certificate' => 'Birth Certificate',
            'custody_agreement' => 'Custody Agreement',
            'medical_form'      => 'Medical Form',
            'other'             => 'Other',
            default             => ucfirst(str_replace('_', ' ', $this->doc_type ?? 'Document')),
        };
    }

    /**
     * Get badge color CSS classes based on document type.
     */
    public function getDocTypeBadgeClassAttribute(): string
    {
        return match ($this->doc_type) {
            'birth_certificate' => 'bg-info-subtle text-info border border-info-subtle',
            'custody_agreement' => 'bg-warning-subtle text-warning border border-warning-subtle',
            'medical_form'      => 'bg-danger-subtle text-danger border border-danger-subtle',
            default             => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }

    /**
     * Check if the document has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }
}
