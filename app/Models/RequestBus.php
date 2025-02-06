<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestBus extends Model
{
    protected $fillable = [
        'from',
        'to',
        'departure',
        'arrival',
        'adults',
        'childreen',
        'adult_price',
        'child_price',
        'bus',
        'bus_number',
        'driver_phone',
        'request_booking_id',
        'notes',
    ];
}
