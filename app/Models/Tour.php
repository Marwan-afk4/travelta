<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'name',
        'arrival',
        'description',
        'video_link',
        'tour_type',
        'status',
        'days',
        'nights',
        'tour_type_id',
        'featured',
        'featured_from',
        'featured_to',
        'deposit',
        'deposit_type',
        'tax',
        'tax_type',
        'pick_up_country_id',
        'pick_up_city_id',
        'pick_up_map',
        'destination_type', 
        'tour_email',
        'tour_website',
        'tour_phone',
        'tour_address',
        'payments_options',
        'policy',
        'cancelation',
        'enabled_extra_price',
        'with_accomodation',
        'enable_person_type',
        'price',
        'currency_id',
    ];
 
    public function destinations(){
        return $this->hasMany(TourDestination::class, 'tour_id');
    }

    public function availability(){
        return $this->hasMany(TourAvailability::class, 'tour_id');
    }

    public function cancelation_items(){
        return $this->hasMany(TourCancelation::class, 'tour_id');
    }

    public function excludes(){
        return $this->hasMany(TourExclude::class, 'tour_id');
    }

    public function includes(){
        return $this->hasMany(TourInclude::class, 'tour_id');
    }

    public function itinerary(){
        return $this->hasMany(TourItinerary::class, 'tour_id');
    }

    public function tour_type(){
        return $this->belongsTo(TourType::class, 'tour_id');
    }

    public function pick_up_country(){
        return $this->belongsTo(Country::class, 'pick_up_country_id');
    }

    public function pick_up_city(){
        return $this->belongsTo(City::class, 'pick_up_city_id');
    }
}
