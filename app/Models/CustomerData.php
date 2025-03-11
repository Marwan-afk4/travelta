<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerData extends Model
{
    protected $fillable =[
        'customer_id',
        'affilate_id',
        'agent_id',
        'total_booking',
        'type',
        'name',
        'phone',
        'email',
        'gender',
        'watts',
        'source_id',
        'agent_sales_id',
        'service_id',
        'nationality_id',
        'country_id',
        'city_id',
        'image',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        return url('storage/' . $this->attributes['image']);
    }

    public function request(){
        return $this->hasOne(RequestBooking::class, 'customer_id', 'customer_id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function source(){
        return $this->belongsTo(CustomerSource::class, 'source_id');
    }

    public function agent_sales(){
        return $this->belongsTo(HrmEmployee::class, 'agent_sales_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function nationality(){
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }
}
