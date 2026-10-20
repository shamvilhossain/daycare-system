<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Reusable invoice line-item name (Description dropdown on the create-invoice form).
 */
class InvoiceItemPreset extends Model
{
    protected $guarded = [];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'usage_count'    => 'integer',
        'last_used_at'   => 'datetime',
        'is_active'      => 'boolean',
    ];

    private const MONTHS = 'jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|june?|july?|aug(?:ust)?|sep(?:t(?:ember)?)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Most-used first, then alphabetical — the dropdown order. */
    public function scopeForDropdown($query)
    {
        return $query->active()->orderByDesc('usage_count')->orderBy('name');
    }

    /**
     * Catalog name for a line-item description: trims and collapses spaces and drops a trailing
     * period such as " - May 2023" / " (Oct 2026)", so "Tuition - May 2023" and
     * "Tuition - June 2023" share one entry ("Tuition").
     */
    public static function normalizeName(?string $description): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', (string) $description));
        $name = preg_replace('/\s*[-–—:,(]?\s*(?:' . self::MONTHS . ')\.?\s*,?\s*\d{4}\s*\)?$/iu', '', $name);

        return trim(mb_substr($name, 0, 255));
    }
}
