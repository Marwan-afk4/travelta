<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currancy extends Model
{
    protected $fillable = [
        'currancy_name',
        'currancy_symbol',
        'currancy_code',
    ];
}
