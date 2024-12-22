<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelTour extends Model
{
    protected $fillable = [
        'tour',
        'type',
        'manuel_booking_id',
        'destination',
        'hotel_name',
        'room_type',
        'check_in',
        'check_out',
        'nights',
        'adults',
        'childreen',
        'adult_price',
        'child_price',
        'transportation',
        'seats',
    ];
}
