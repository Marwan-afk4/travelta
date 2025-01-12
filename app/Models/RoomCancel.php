<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomCancel extends Model
{
    protected $fillable = [
        'room_id',
        'amount',
        'type',
        'before',
    ];
}
