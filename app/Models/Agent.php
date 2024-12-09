<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;

class Agent extends Model
{
    use HasApiTokens,HasFactory, Notifiable;
    protected $fillable = [
        'plan_id',
        'f_name',
        'l_name',
        'agent',
        'address',
        'country_id',
        'city_id',
        'source_id',
        'phone',
        'email',
        'password',
        'phone_of_owner',
        'date_of_join',
        'total_booking',
        'total_commission',
        'legal_paper',
        'start_date',
        'end_date',
        'price_cycle',
    ];
    protected $appends = ['role'];

    public function getRoleAttribute(){
        return 'agent';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
