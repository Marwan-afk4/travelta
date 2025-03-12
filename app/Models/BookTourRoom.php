<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTourRoom extends Model
{


    protected $fillable = [
        'book_tour_id',
        'to_hotel_id',
        'single_room_count',
        'double_room_count',
        'triple_room_count',
        'quad_room_count',
    ];

    public function bookTour()
    {
        return $this->belongsTo(BookTourengine::class, 'book_tour_id');
    }

    public function to_hotel()
    {
        return $this->belongsTo(TourHotel::class, 'to_hotel_id');
    }
}
