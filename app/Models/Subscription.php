<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{


    protected $fillable = [
        'plan_id',
        'agent_id',
        'start_date',
        'renual_date',
    ];
}
