<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPaper extends Model
{
    protected $fillable = [
        'image',
        'type',
        'agent_id',
        'affilate_id',
        'user_id',
        'customer_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function agent(){
        return $this->belongsTo(Agent::class,'agent_id');
    }

    public function affilate(){
        return $this->belongsTo(AffilateAgent::class,'affilate_id');
    }
}
