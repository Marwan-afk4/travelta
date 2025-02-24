<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAgent extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'position_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    public function position(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }

    public function user_positions(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }
}
