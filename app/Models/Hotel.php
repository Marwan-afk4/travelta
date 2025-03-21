<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    protected $fillable =[
        'hotel_name',
        'description',
        'email',
        'phone_number',
        'hotel_logo',
        'country_id',
        'city_id',
        'zone_id',
        'stars',
        'hotel_video_link',
        'hotel_website',
        'check_in',
        'check_out',
        'location'
    ];



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

    public function acceptedCards()
{
    return $this->belongsToMany(AcceptedCard::class, 'hotel_accepted_cards', 'hotel_id', 'card_id');
}

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'hotel_features');
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class);
    }


    public function policies()
    {
        return $this->hasMany(HotelPolicy::class);
    }

    public function rooms(){
        return $this->hasMany(Room::class);
    }
}
