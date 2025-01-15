<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAgency extends Model
{
    protected $fillable = [
        'room_id',
        'agency_code',
        'percentage',
    ];
}
