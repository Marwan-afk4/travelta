<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPayable extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'supplier_id',
        'manuel_booking_id',
        'currency_id',
        'paid',
        'payable',
        'due_date',
        'manuel_date',
    ];

    public function supplier(){
        return $this->belongsTo(SupplierAgent::class, 'supplier_id');
    }

    public function manuel(){
        return $this->belongsTo(ManuelBooking::class, 'manuel_booking_id');
    } 

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
