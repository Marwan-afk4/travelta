<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{


    protected $fillable = [
        'name',
        'city_id',
        'country_id',
    ];

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function hotels(){
        return $this->hasMany(Hotel::class);
    }
}
