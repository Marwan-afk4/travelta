<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomExtra extends Model
{
    protected $fillable = [
        'name',
        'thumbnail',
        'price',
        'status',
        'hotel_id',
        'affilate_id',
        'agent_id',
    ];
}
