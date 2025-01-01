<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'age',
        'first_name',
        'last_name',
        'manuel_booking_id',
    ];
}
