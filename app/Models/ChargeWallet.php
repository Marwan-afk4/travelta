<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargeWallet extends Model
{
    protected $fillable = [
        'wallet_id',
        'agent_id',
        'affilate_id',
        'payment_method_id',
        'amount',
        'image',
        'status',
    ];
}
