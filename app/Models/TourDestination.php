<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourDestination extends Model
{
    protected $fillable = [
        'tour_id',
        'country_id',
        'city_id',
        'arrival_map',
    ];
}
