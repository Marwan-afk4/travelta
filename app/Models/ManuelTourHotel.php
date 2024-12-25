<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelTourHotel extends Model
{
    protected $fillable = [
        'destination',
        'manuel_tour_id',
        'hotel_name',
        'room_type',
        'check_in',
        'check_out',
        'nights',
    ];
}
