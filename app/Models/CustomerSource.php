<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSource extends Model
{
    protected $fillable = [
        'source',
        'status',
    ];
}
