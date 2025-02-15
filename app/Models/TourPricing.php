<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPricing extends Model
{
    protected $fillable = [
        'tour_id',
        'person_type',
        'min_age',
        'max_age',
    ];
}
