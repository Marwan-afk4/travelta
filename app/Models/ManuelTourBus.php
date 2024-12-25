<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelTourBus extends Model
{
    protected $fillable = [
        'transportation',
        'manuel_tour_id',
        'seats',
    ];
}
