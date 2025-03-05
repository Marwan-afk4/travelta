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
        'source_id',
        'agent_sales_id',
        'service_id',
        'nationality_id',
        'country_id',
        'city_id',
        'image',
        'status',
    ];

    public function manuel(){
        return $this->hasMany(ManuelBooking::class, 'to_customer_id');
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
