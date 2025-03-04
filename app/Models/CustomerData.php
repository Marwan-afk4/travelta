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
        'email',
        'gender',
        'watts',
        'source_id',
        'agent_sales_id',
        'service_id',
        'nationality_id',
        'country_id',
        'city_id',
        'image',
        'status',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
