<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryTax extends Model
{
    protected $fillable =[
        'name',
        'country_id',
        'type',
        'amount',
    ];
}
