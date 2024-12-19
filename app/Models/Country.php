<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable =[
        'name',
    ];

    public function cities(){
        return $this->hasMany(City::class);
    }

    public function hotels(){
        return $this->hasMany(Hotel::class);
    }

    public function zones(){
        return $this->hasMany(Zone::class);
    }
}
