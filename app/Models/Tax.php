<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'country_id',
        'type',
        'amount',
        'agent_id',
        'affilate_id',
    ];
}
