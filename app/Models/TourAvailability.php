<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourAvailability extends Model
{
    protected $fillable = [
        'tour_id',
        'date',
        'last_booking',
        'quantity',
    ];
}
