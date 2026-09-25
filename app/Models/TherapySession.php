<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapySession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    public function therapist()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    public function service()
    {
        return $this->belongsTo(TherapyService::class, 'therapy_service_id');
    }
    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    /**
     * The invoice line item this session was billed on (if any).
     * A session can only be billed once — used for dedup via whereDoesntHave('invoiceItem').
     */
    public function invoiceItem()
    {
        return $this->hasOne(InvoiceItem::class);
    }
  
    public function childTherapyPackage() { 
        return $this->belongsTo(ChildTherapyPackage::class); 
    }
}
