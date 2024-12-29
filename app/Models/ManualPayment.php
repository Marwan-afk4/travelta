<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPayment extends Model
{


    protected $fillable = [
        'payment_method_id',
        'affilate_agent_id',
        'agency_id',
        'plan_id',
        'start_date',
        'end_date',
        'amount',
        'receipt',
        'status',
    ];

    public function affilate_agent(){
        return $this->belongsTo(AffilateAgent::class);
    }

    public function agent(){
        return $this->belongsTo(Agent::class,'agency_id');
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class);
    }
}
