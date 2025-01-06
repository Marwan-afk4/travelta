<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    //



    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function themes()
    {
        return $this->belongsToMany(Theme::class,'hotel_themes');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class,'hotel_facilities');
    }

    public function accepted_cards()
    {
        return $this->belongsToMany(AcceptedCard::class,'hotel_accepted_cards');
    }
}
