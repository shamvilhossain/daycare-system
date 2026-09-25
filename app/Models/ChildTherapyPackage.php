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

    public function isExhausted(): bool
    {
        return $this->items->every(fn ($item) =>
            $this->remainingFor($item->service) === 0
        );
    }
}
