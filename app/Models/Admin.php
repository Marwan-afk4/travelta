<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{


    protected $fillable = [
        'admin_position_id',
        'zone_id',
        'name',
        'email',
        'password',
        'phone_number',
        'legal_paper'
    ];
}
