<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{


    protected $fillable = [
        'plan_id',
        'name',
        'phone',
        'email',
        'phone_of_owner',
        'date_of_join',
        'total_booking',
        'total_commission',
        'legal_paper',
    ];
}
