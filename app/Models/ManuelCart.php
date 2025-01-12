<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelCart extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'total',
        'payment',
        'payment_method_id',
        'image',
        'status',
    ];
}
