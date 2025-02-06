<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTour extends Model
{
    protected $fillable = [
        'tour',
        'type', 
        'flight_date',
        'adult_price',
        'child_price',
        'adults',
        'childreen',
        'request_booking_id',
        'notes',
    ];

    public function bus(){
        return $this->hasMany(RequestTourBus::class, 'request_tour_id');
    }

    public function hotel(){
        return $this->hasMany(RequestTourHotel::class, 'request_tour_id');
    }
}
