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
        'first_time',
        'supplier_id',
        'agent_id',
        'affilate_id',
        'to_customer_id',
    ];
    protected $appends = ['invoice_link'];

    public function booking_engine()
    {
       return $this->morphTo();
    }

    public function getInvoiceLinkAttribute(){
        return url('storage/' . $this->invoice);
    }

    public function financial(){
        return $this->belongsTo(FinantiolAcounting::class, 'financial_id');
    }

    public function manuel_booking(){
        return $this->belongsTo(ManuelBooking::class, 'manuel_booking_id');
    }
}
