<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingengineList extends Model
{
    protected $fillable =[
        'room_id',
        'affilate_id',
        'agent_id',
        'from_supplier_id',
        'country_id',
        'city_id',
        'to_agent_id',
        'to_customer_id',
        'hotel_id',
        'amount',
        'currency_id',
        'check_in',
        'check_out',
        'room_type',
        'no_of_adults',
        'no_of_children',
        'no_of_nights',
        'payment_status',
        'code',
        'status',
        'special_request',
        'currancy_id',
        'request_status',
        'cart_status',
        'count',
    ];
    protected $appends = ['to_client'];

    public function children()
    {
        return $this->morphMany(ChildrenEngine::class, 'booking_engine');
    }

    public function adult()
    {
        return $this->morphMany(AdultEngine::class, 'booking_engine');
    }

    public function payment_carts()
    {
        return $this->morphMany(ManuelCart::class, 'booking_engine');
    }

    public function upcoming_payment_carts()
    {
        return $this->morphMany(PaymentsCart::class, 'booking_engine');
    }

    public function booking_payment()
    {
        return $this->morphMany(BookingPayment::class, 'booking_engine');
    }

    public function tasks(){
        return $this->hasMany(BookingTask::class, 'booking_engine_id');
    }

    public function operation_confirmed(){
        return $this->hasMany(OperationBookingConfirmed::class, 'booking_engine_id');
    }

    public function operation_vouchered(){
        return $this->hasMany(OperationBookingVouchered::class, 'booking_engine_id');
    }

    public function operation_canceled(){
        return $this->hasMany(OperationBookingCanceled::class, 'booking_engine_id');
    }

    public function agent(){
        return $this->belongsTo(Agent::class,'agent_id');
    }


    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function affilate(){
        return $this->belongsTo(AffilateAgent::class,'affilate_id');
    }

    public function from_supplier(){
        return $this->belongsTo(Agent::class,'from_supplier_id');
    }
    public function getToClientAttribute(){
        if (!empty($this->attributes['to_agent_id'])) {
            return $this->belongsTo(Agent::class, 'to_agent_id')->first();
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
    public function room(){
        return $this->belongsTo(Room::class,'room_id');
    }
}
