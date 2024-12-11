<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAgent extends Model
{
    protected $fillable =[
        'name',
        'phone',
        'email',
        'agent_id',
    ];
}
