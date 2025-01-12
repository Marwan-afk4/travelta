<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAmenity extends Model
{
    protected $fillable = [
        'name',
        'selected',
        'status',
        'affilate_id',
        'agent_id',
    ];
}
