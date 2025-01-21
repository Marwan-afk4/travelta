<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerData extends Model
{
    protected $fillable =[
        'customer_id',
        'affilate_id',
        'agent_id',
        'total_booking',
        'type',
        'name',
        'phone',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
