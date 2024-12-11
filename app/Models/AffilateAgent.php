<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffilateAgent extends Model
{
    protected $fillable =[
        'f_name',
        'l_name',
        'email',
        'phone',
        'password',
    ];
}
