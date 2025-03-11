<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourHotel extends Model
{
    protected $fillable = [
        'tour_id',
        'name',
    ];

    public function bookTour(){
        return $this->hasMany(BookTourengine::class, 'to_hotel_id');
    }
}
