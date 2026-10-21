<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildTherapyPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'purchased_at'   => 'date',
            'expires_at'     => 'date',
        ];
    }

    public function child()   { return $this->belongsTo(Child::class); }
    public function package() { return $this->belongsTo(TherapyPackage::class, 'therapy_package_id'); }
    public function items()   { return $this->hasMany(ChildTherapyPackageItem::class); }
    public function sessions(){ return $this->hasMany(TherapySession::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }

    // Remaining balance for one therapy type — computed live, from last turn's design
    public function remainingFor(TherapyService $service): int
    {
        $included = $this->items()->where('therapy_service_id', $service->id)->value('sessions_included') ?? 0;
        $used = $this->sessions()
            ->where('therapy_service_id', $service->id)
            ->where('status', 'completed')
            ->count();

        return max(0, $included - $used);
    }

    /** Sessions included for one therapy type (0 when the package doesn't cover it). */
    public function includedFor(int $serviceId): int
    {
        return (int) ($this->items()->where('therapy_service_id', $serviceId)->value('sessions_included') ?? 0);
    }

    /** Sessions already taken from the balance: booked (scheduled) or done (completed). */
    public function bookedOrUsedFor(int $serviceId, ?int $excludeSessionId = null): int
    {
        return $this->sessions()
            ->where('therapy_service_id', $serviceId)
            ->whereIn('status', ['scheduled', 'completed'])
            ->when($excludeSessionId, fn ($q) => $q->where('id', '!=', $excludeSessionId))
            ->count();
    }

    /** Sessions still free to book — remaining minus sessions already scheduled. */
    public function availableFor(int $serviceId, ?int $excludeSessionId = null): int
    {
        return max(0, $this->includedFor($serviceId) - $this->bookedOrUsedFor($serviceId, $excludeSessionId));
    }

    /** Active packages covering a therapy type and valid on a date, oldest purchase first (FIFO). */
    public function scopeUsableFor($query, int $childId, int $serviceId, string $date)
    {
        return $query->where('child_id', $childId)
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', $date))
            ->whereDate('purchased_at', '<=', $date)
            ->whereHas('items', fn ($q) => $q->where('therapy_service_id', $serviceId))
            ->orderBy('purchased_at')
            ->orderBy('id');
    }

    public function isExhausted(): bool
    {
        return $this->items->every(fn ($item) =>
            $this->remainingFor($item->service) === 0
        );
    }
}
