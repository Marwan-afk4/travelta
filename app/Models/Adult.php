<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adult extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'manuel_booking_id',
    ];
}
