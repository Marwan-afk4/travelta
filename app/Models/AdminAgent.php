<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAgent extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'name',
        'email',
        'phone',
        'password',
    ];
}
