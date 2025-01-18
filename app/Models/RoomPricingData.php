<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPricingData extends Model
{
    protected $fillable = [
        'room_type',
        'meal_plan',
        'adults',
        'children',
    ];
}
