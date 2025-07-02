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
        'users',
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
        'zone_id',
        'source_id',
        'owner_name',
        'owner_phone',
        'owner_email',
        'status',
        'image',
    ];
    protected $appends = ['image_link'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getImageLinkAttribute(){
        if(!empty($this->image)){
            return url('storage/' . $this->image);
        }
        return null;
    }

    public function legal_papers(){
        return $this->hasMany(LegalPaper::class,'agent_id');
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function zone(){
        return $this->belongsTo(Zone::class);
    }

    public function manualpayment(){
        return $this->hasMany(ManualPayment::class);
    }

    public function tour(){
        return $this->hasMany(Tour::class);
    }
}
