<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEngine extends Model
{
    protected $fillable = [
        'room_id',
        'check_in',
        'check_out',
        'quantity',
    ];

    public function room(){
        return $this->belongsTo(Room::class);
    }

    public function room_availability(){
        return $this->hasMany(RoomAvailability::class, 'room_id');
    }
}
