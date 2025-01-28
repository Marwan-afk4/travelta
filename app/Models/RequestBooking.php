<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestBooking extends Model
{ 
    protected $fillable = [
        'customer_id',
        'admin_agent_id',
        'service_id',
        'currency_id',
        'affilate_id',
        'agent_id',
        'expected_revenue',
        'priority',
        'stages',
    ];
}
