<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTask extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'manuel_booking_id',
        'booking_engine_id', 
        'engine_tour_id', 
        'notes',
        'confirmation_number',
        'notification',
    ];

}