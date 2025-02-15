<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourExtra extends Model
{
    protected $fillable = [
        'tour_id',
        'name',
        'price',
        'currency_id',
        'type',
    ];
}
