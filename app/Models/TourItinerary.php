<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourItinerary extends Model
{
    protected $fillable = [
        'tour_id',
        'image',
        'day_name',
        'day_description',
        'content',
    ];
}
