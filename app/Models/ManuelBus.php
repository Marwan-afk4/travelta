<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelBus extends Model
{
    protected $fillable = [
        'from',
        'to',
        'manuel_booking_id',
        'departure',
        'arrival',
        'adults',
        'childreen',
        'adult_price',
        'child_price',
        'bus',
        'bus_number',
        'driver_phone',
    ];
}
