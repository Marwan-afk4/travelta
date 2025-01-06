<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{

    protected $fillable =[
        'name'
    ];


    public function hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotel_themes');
    }
}
