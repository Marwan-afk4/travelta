<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestAdult extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'request_booking_id',
    ];
}
