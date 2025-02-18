<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPrice extends Model
{
    protected $fillable = [
        'tour_id',
        'price',
        'currency_id',
    ];
}
