<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourCancelation extends Model
{
    protected $fillable = [
        'tour_id',
        'type',
        'amount',
        'days',
    ];
}
