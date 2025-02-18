<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'currency_id',
        'name',
        'phone',
        'balance',
    ];

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
