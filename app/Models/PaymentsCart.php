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
    ];
    protected $appends = ['due_payment'];

    public function getDuePaymentAttribute(){
        return $this->attributes['amount'] - $this->attributes['payment'];
    }
}
