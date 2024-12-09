<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;



class User extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'emergency_phone',
        'legal_paper',
        'type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
