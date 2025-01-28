<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHotel extends Model
{
    protected $fillable = [
        'check_in',
        'check_out',
        'manuel_booking_id',
        'nights',
        'hotel_name',
        'room_type',
        'room_quantity',
        'adults',
        'childreen',
        'request_booking_id',
        'notes',
    ];
}
