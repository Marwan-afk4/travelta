<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelBooking extends Model
{
    protected $fillable = [
        'to_supplier_id',
        'to_customer_id',
        'from_supplier_id',
        'from_service_id',
        'cost',
        'price',
        'currency_id',
        'tax_type',
        'total_price',
        'country_id',
        'city_id',
        'mark_up',
        'mark_up_type',
        'affilate_id',
        'agent_id',
    ];

    public function taxes(){
        return $this->belongsToMany(Tax::class, 'manuel_taxes', 'manuel_id', 'tax_id');
    }

    public function from_supplier(){
        return $this->belongsTo(SupplierAgent::class, 'to_supplier_id');
    }

    public function to_client(){
        return !empty($this->belongsTo(SupplierAgent::class, 'to_supplier_id'))
        ?? $this->belongsTo(Customer::class, 'to_customer_id');
    }

    public function hotel(){
        return $this->hasOne(ManuelHotel::class, 'manuel_booking_id');
    }

    public function bus(){
        return $this->hasOne(ManuelBus::class, 'manuel_booking_id');
    }

    public function flight(){
        return $this->hasOne(ManuelFlight::class, 'manuel_booking_id');
    }

    public function tour(){
        return $this->hasOne(ManuelTour::class, 'manuel_booking_id');
    }

    public function visa(){
        return $this->hasOne(ManuelVisa::class, 'manuel_booking_id');
    }
}
