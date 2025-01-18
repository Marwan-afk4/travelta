<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    protected $fillable = [
        'room_id',
        'from',
        'to',
        'quantity_rooms',
    ];
}
