<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcceptedCard extends Model
{

    protected $fillable = [
        'card_name',
        'logo'
    ];


    public function hotels()
    {
        return $this->belongsToMany(Hotel::class,'hotel_accepted_cards');
    }
}
