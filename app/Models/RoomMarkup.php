<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMarkup extends Model
{
    protected $fillable = [
        'room_id',
        'agent_code',
        'percentage',
    ];
}
