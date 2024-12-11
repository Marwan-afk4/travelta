<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'plan_id',
        'name',
        'phone',
        'email',
        'address',
        'password',
        'total_booking',
        'total_commission',
        'start_date',
        'end_date',
        'price_cycle',
        'role',
        'country_id',
        'city_id',
        'source_id',
        'owner_name',
        'owner_phone',
        'owner_email',
    ];

    protected $hidden = [
        'password', 
    ];

    protected function casts(): array
    {
        return [ 
            'password' => 'hashed',
        ];
    }

    public function legal_papers(){
        return $this->hasMany(LegalPaper::class);
    }
}
