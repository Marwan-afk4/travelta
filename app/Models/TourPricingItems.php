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
    protected $appends = ['price_after_tax'];

    public function currency()
    {
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }public function getPriceAfterTaxAttribute()
    {
        $tour = $this->tour;
        $price = $this->attributes['price'];
    
        if (!$tour) {
            return $price; 
        }
    
        $tax = $tour->tax;
        $tax_type = $tour->tax_type;
    
        if ($tax_type == 'precentage') {
            return $price + ($price * $tax / 100);
        } else {
            return $price + $tax;
        }
    }
    
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}
