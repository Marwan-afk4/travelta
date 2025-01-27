<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPricing extends Model
{
    protected $fillable = [
        'room_id',
        'name',
        'from',
        'to',
        'price',
        'currency_id',
        'pricing_data_id',
    ];

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function pricing_data(){
        return $this->belongsTo(RoomPricingData::class, 'pricing_data_id');
    }

    public function groups(){
        return $this->belongsToMany(RoomPricingData::class, 'pricing_data_id');
    }
}
