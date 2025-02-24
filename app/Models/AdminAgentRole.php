<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAgentRole extends Model
{
    protected $fillable =[
        'position_id',
        'module',
        'action',
    ];

    public function roles(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }
}
