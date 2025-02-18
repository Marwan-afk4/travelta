<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerTransaction extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'owner_id',
        'currency_id',
        'amount',
        'type',
        'financial',
    ];

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function owner(){
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
