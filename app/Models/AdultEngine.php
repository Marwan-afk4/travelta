<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdultEngine extends Model
{
    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'phone',
    ];

    public function booking_engine()
    {
       return $this->morphTo();
    }
}
