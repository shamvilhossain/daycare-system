<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapySession extends Model
{
    protected $guarded = [];
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
}
