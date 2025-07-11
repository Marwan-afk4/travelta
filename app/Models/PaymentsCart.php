<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentsCart extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'supplier_id',
        'agent_id',
        'affilate_id',
        'amount',
        'date',
        'payment',
        'image',
        'status',
        'to_customer_id', 
    ];
    protected $appends = ['due_payment'];

    public function booking_engine()
    {
       return $this->morphTo();
    }

    public function getDuePaymentAttribute(){
        return $this->attributes['amount'] - $this->attributes['payment'];
    }

    public function manuel_booking(){
        return $this->belongsTo(ManuelBooking::class, 'manuel_booking_id');
    }
}
