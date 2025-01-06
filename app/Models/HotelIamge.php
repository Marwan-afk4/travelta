<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelIamge extends Model
{
    protected $fillable =[
        'hotel_id',
        'iamge',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
