<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelTour extends Model
{
    protected $fillable = [
        'tour',
        'type',
        'manuel_booking_id',
        'adult_price',
        'child_price',
        'adults',
        'childreen', 
    ];

    public function hotel(){
        return $this->hasMany(ManuelTourHotel::class, 'manuel_tour_id');
    }
}
