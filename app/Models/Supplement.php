<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    protected $fillable = [
        'room_id',
        'name',
        'type',
        'price',
        'currency_id',
    ];
}
