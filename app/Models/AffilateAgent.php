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
        return $this->hasMany(LegalPaper::class, 'affilate_id');
    }
}
