<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable =[
        'name',
        'phone',
        'role',
        'email',
        'gender',
        'emergency_phone',
        'password',
        'watts',
        'service_id',
        'nationality_id',
        'country_id',
        'city_id',
        'image',
        'status',
    ];
    protected $appends = ['image_link'];

    public function getImageLinkAttribute(){
        if (isset($this->attributes['image'])) {
            return url('storage/' . $this->attributes['image']);
        }
        return null;
    }

    public function manuel(){
        return $this->hasMany(ManuelBooking::class, 'to_customer_id');
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

    public function agent_customer(){
        return $this->hasMany(CustomerData::class, 'customer_id');
    }

    protected $hidden = [
        'password',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
