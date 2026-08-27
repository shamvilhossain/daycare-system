<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public function parent()  { return $this->belongsTo(ParentProfile::class, 'parent_id'); }
    public function child()   { return $this->belongsTo(Child::class); }
    public function items()   { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
