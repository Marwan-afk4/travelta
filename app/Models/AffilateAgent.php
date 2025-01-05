<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class AffilateAgent extends Model
{
    use HasApiTokens,HasFactory, Notifiable;
    protected $fillable =[
        'f_name',
        'l_name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'plan_id',
        'start_date',
        'end_date',
        'price_cycle',
    ];
    protected $appends = ['name'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(){
        return $this->attributes['f_name'] . ' ' . $this->attributes['l_name'];
    }

    public function legal_papers(){
        return $this->hasMany(LegalPaper::class,'affilate_id');
    }
    public function manualpayment(){
        return $this->hasMany(ManualPayment::class, 'affilate_agent_id');
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }
}
