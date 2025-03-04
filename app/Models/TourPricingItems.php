<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPricingItems extends Model
{
    protected $fillable = [
        'tour_pricing_id',
        'currency_id',
        'price',
        'type',
        'tour_id'
    ];
}
