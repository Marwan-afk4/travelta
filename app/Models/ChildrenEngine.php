<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildrenEngine extends Model
{
    protected $fillable = [
        'age',
        'first_name',
        'last_name', 
    ];

    public function booking_engine()
    {
       return $this->morphTo();
    }
}
