<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    protected $fillable = [
        'country_id',
        'city_id',
        'zone_id',
        'hotel_name',
        'email',
        'phone_number',
        'rating',
        'image',
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function zone(){
        return $this->belongsTo(Zone::class);
    }
}
