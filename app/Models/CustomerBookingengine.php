<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBookingengine extends Model
{


    protected $fillable = [
        'customer_id',
        'country_id',
        'city_id',
        'nationality_id',
        'booking_engine_id',
        'check_in',
        'check_out',
        'rooms',
        'adults',
        'children',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function booking_engine(){
        return $this->belongsTo(BookingEngine::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function nationality(){
        return $this->belongsTo(Nationality::class);
    }
}
