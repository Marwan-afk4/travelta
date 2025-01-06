<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcceptedCard extends Model
{
    //


    public function hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotel_accepted_cards');
    }
}
