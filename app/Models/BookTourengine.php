<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTourengine extends Model
{
    protected $fillable =[
        'affilate_id',
        'agent_id',
        'date',
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

    public function operation_confirmed(){
        return $this->hasMany(OperationBookingConfirmed::class, 'engine_tour_id');
    }

    public function operation_vouchered(){
        return $this->hasMany(OperationBookingVouchered::class, 'engine_tour_id');
    }

    public function operation_canceled(){
        return $this->hasMany(OperationBookingCanceled::class, 'engine_tour_id');
    }

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
        return $this->hasMany(BookTourExtra::class, 'book_tour_id');
    }

    public function book_tour_room(){
        return $this->hasMany(BookTourRoom::class, 'book_tour_id');
    }
}
