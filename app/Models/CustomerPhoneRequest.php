<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPhoneRequest extends Model
{
    protected $fillable =[
        'customer_id',
        'affilate_id',
        'agent_id',
        'old_phone',
        'new_phone',
        'status',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
