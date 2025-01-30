<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelVisa extends Model
{
    protected $fillable = [
        'country',
        'travel_date',
        'appointment_date',
        'notes',
        'manuel_booking_id',
        'adults',
        'childreen',
    ];
}
