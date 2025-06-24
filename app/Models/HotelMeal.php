<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelMeal extends Model
{
    protected $fillable =[
        'hotel_id',
        'meal_name',
    ];

    public function hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
