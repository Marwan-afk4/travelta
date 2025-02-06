<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTourBus extends Model
{
    protected $fillable = [
        'transportation', 
        'seats',
        'departure',
        'request_tour_id', 
    ];
}
