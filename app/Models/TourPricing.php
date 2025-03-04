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
    
    public function tour_pricing_items(){
        return $this->hasMany(TourPricingItems::class, 'tour_pricing_id');
    }
}
