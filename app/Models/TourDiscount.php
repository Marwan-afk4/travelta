<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourDiscount extends Model
{
    protected $fillable = [
        'tour_id',
        'from',
        'to',
        'discount',
        'type',
    ];
}
