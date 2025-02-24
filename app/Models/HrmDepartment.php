<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmDepartment extends Model
{
    protected $fillable =[
        'affilate_id',
        'agent_id',
        'name',
        'status',
    ];
}
