<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{

    protected $fillable = [
        'name',
        'logo'
    ];


    public function hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotel_facilities');
    }
}
