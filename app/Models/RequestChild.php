<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestChild extends Model
{
    protected $fillable = [
        'age',
        'first_name',
        'last_name',
        'request_booking_id',
    ];
}
