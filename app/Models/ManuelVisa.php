<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelVisa extends Model
{
    protected $fillable = [
        'tour',
        'type',
        'manuel_booking_id', 
    ];
}
