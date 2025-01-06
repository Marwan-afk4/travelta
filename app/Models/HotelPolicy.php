<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPolicy extends Model
{

    protected $fillable =[
        'hotel_id',
        'title',
        'description',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
