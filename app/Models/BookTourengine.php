<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTourengine extends Model
{


    protected $fillable =[
        'affilate_id',
        'agent_id',
        'tour_id',
        'from_supplier_id',
        'country_id',
        'to_hotel_id',
        'currency_id',
        'to_name',
        'to_email',
        'to_phone',
        'to_role',
        'no_of_people',
        'code',
        'total_price',
        'status',
        'payment_status',
        'special_request',
        'request_status',
    ];

    public function tour(){
        return $this->belongsTo(Tour::class);
    }

    public function from_supplier(){
        return $this->belongsTo(Agent::class, 'from_supplier_id');
    }

    public function to_hotel(){
        return $this->belongsTo(TourHotel::class, 'to_hotel_id');
    }

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function book_tour_extra(){
        return $this->hasMany(BookTourExtra::class);
    }

    public function book_tour_room(){
        return $this->hasMany(BookTourRoom::class);
    }
}
