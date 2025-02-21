<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAgentPosition extends Model
{
    protected $fillable =[
        'name',
        'affilate_id',
        'agent_id',
    ];

    public function perimitions(){
        return $this->hasMany(AdminAgentRole::class, 'position_id');
    }
}
