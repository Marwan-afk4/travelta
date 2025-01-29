<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestVisa extends Model
{
    protected $fillable = [
        'country',
        'travel_date',
        'appointment_date',
        'notes',
        'number',
        'request_booking_id', 
        'adults',
        'childreen',
    ];
}
