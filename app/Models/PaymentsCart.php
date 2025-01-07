<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentsCart extends Model
{
    protected $fillable = [
        'manuel_cart_id',
        'amount',
        'date',
        'payment',
        'image',
        'status',
    ];
}
