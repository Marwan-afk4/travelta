<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Model
{


    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'emergency_phone',
        'legal_paper',
        'type'
    ];
}
