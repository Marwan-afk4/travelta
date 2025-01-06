<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotel_features');
    }
}
