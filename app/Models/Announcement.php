<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    /**
     * Relationship to the User who created the announcement.
     * (staff_id stores the authenticated user's ID)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Legacy/fallback relation to Staff model.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get the author display name.
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->creator) {
            return $this->creator->name;
        }

        if ($this->staff) {
            return $this->staff->full_name;
        }

        return 'System Administrator';
    }

    /**
     * Get status string: active, scheduled, expired.
     */
    public function getStatusAttribute(): string
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        if ($this->published_at && $this->published_at->isFuture()) {
            return 'scheduled';
        }

        return 'active';
    }

    /**
     * Get status badge HTML markup.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Active</span>',
            'scheduled' => '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-clock-fill me-1"></i>Scheduled</span>',
            'expired'   => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i>Expired</span>',
            default     => '<span class="badge bg-light text-dark">Unknown</span>',
        };
    }

    /**
     * Get audience badge HTML markup.
     */
    public function getAudienceBadgeAttribute(): string
    {
        return match ($this->audience) {
            'all'     => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-globe me-1"></i>All</span>',
            'parents' => '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-people-fill me-1"></i>Parents</span>',
            'staff'   => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-person-badge-fill me-1"></i>Staff</span>',
            default   => '<span class="badge bg-light text-dark">' . e(ucfirst($this->audience)) . '</span>',
        };
    }

    /**
     * Scope query to only active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })->where(function ($q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        });
    }

    /**
     * Scope query to announcements for the public welcome page.
     * Audience: parents or all, expires_at > now().
     */
    public function scopeForWelcome($query)
    {
        return $query->whereIn('audience', ['parents', 'all'])
            ->where('expires_at', '>', now())
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }
}
