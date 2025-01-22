<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEngine extends Model
{
    protected $fillable = [
        'room_id',
        'check_in',
        'check_out',
        'quantity',
    ];
}
