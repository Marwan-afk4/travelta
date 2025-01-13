<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelMeal extends Model
{
    protected $fillable =[
        'hotel_id',
        'meal_name',
    ];
}
