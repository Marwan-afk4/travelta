<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'date',
        'invoice',
        'amount',
        'financial_id',
        'code',
        'supplier_id',
        'agent_id',
        'affilate_id',
    ];

    public function financial(){
        return $this->belongsTo(FinantiolAcounting::class, 'financial_id');
    }

    public function manuel_booking(){
        return $this->belongsTo(ManuelBooking::class, 'manuel_booking_id');
    }
}
