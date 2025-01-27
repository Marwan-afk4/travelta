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
        return $this->belongsToMany(Group::class, 'group_pricings', 'pricing_id', 'group_id');
    }

    public function nationality(){
        return $this->belongsToMany(Nationality::class, 'nationality_pricings', 'pricing_id', 'nationality_id');
    }
}
