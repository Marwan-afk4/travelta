<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPayment extends Model
{
    protected $fillable = [
        'agent_id', 
        'affilate_id', 
        'supplier_id', 
        'amount', 
        'type', 
        'date', 
        'code', 
    ];
}
