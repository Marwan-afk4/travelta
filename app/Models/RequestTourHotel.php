<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTourHotel extends Model
{
    protected $fillable = [
        'destination',  
        'hotel_name',
        'room_type',
        'check_in',
        'check_out',
        'nights',
        'request_tour_id', 
    ];
}
