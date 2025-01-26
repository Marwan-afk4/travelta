<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'date',
        'amount',
        'financial_accounting_id',
        'code',
    ];

    public function financial(){
        return $this->belongsTo(FinantiolAcounting::class, 'financial_accounting_id');

    }
}
