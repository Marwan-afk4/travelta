<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingengineList extends Model
{
    protected $fillable =[
        'affilate_id',
        'agent_id',
        'from_supplier_id',
        'country_id',
        'city_id',
        'to_agent_id',
        'to_customer_id',
        'hotel_id',
        'check_in',
        'check_out',
        'room_type',
        'no_of_adults',
        'no_of_children',
        'no_of_nights',
        'payment_status',
        'code',
        'status',
    ];
    protected $appends = ['to_client'];

    public function from_supplier(){
        return $this->belongsTo(Agent::class,'from_supplier_id');
    } 
    public function getToClientAttribute(){
        if (!empty($this->attributes['to_supplier_id'])) {
            return $this->belongsTo(SupplierAgent::class, 'to_supplier_id')->first();
        }
        return $this->belongsTo(Customer::class, 'to_customer_id')->first();
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function city(){
        return $this->belongsTo(City::class,'city_id');
    }
    public function to_agent(){
        return $this->belongsTo(Agent::class,'to_agent_id');
    }
    public function to_customer(){
        return $this->belongsTo(Customer::class,'to_customer_id');
    }
    public function hotel(){
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
}
