<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyAgent extends Model
{
    protected $fillable = [
        'currancy_id',
        'affilate_id',
        'agent_id',
        'name',
        'point',
    ];
}
