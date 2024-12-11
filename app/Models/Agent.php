<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'plan_id',
        'name',
        'phone',
        'email',
        'address',
        'password',
        'total_booking',
        'total_commission',
        'start_date',
        'end_date',
        'price_cycle',
        'role',
        'country_id',
        'city_id',
        'source_id',
    ];
}
