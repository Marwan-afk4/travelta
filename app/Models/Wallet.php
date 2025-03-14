<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'currancy_id',
        'amount',
    ];

    public function currancy(){
        return $this->belongsTo(Currancy::class, 'currancy_id');
    }

    public function pendding_wallet(){
        return $this->hasMany(ChargeWallet::class, 'wallet_id')
        ->where('status', 'pending');
    }
}
