<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestBooking extends Model
{ 
    protected $fillable = [
        'customer_id',
        'admin_agent_id',
        'service_id',
        'currency_id',
        'affilate_id',
        'agent_id',
        'expected_revenue',
        'priority',
        'stages',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function admin_agent(){
        return $this->belongsTo(AdminAgent::class, 'admin_agent_id');
    }

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function adults(){
        return $this->hasMany(RequestAdult::class, 'request_booking_id');
    }

    public function children(){
        return $this->hasMany(RequestChild::class, 'request_booking_id');
    }

    public function hotel(){
        return $this->hasOne(RequestHotel::class, 'request_booking_id');
    }

    public function bus(){
        return $this->hasOne(RequestBus::class, 'request_booking_id');
    }

    public function flight(){
        return $this->hasOne(RequestFlight::class, 'request_booking_id');
    }

    public function tour(){
        return $this->hasOne(RequestTour::class, 'request_booking_id');
    }

    public function visa(){
        return $this->hasOne(RequestVisa::class, 'request_booking_id');
    }
}
