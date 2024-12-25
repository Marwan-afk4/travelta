<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelBooking extends Model
{
    protected $fillable = [
        'to_supplier_id',
        'to_customer_id',
        'from_supplier_id',
        'from_service_id',
        'cost',
        'currency_id',
        'tax_type',
        'tax_id',
        'total_price',
        'country_id',
        'city_id',
    ];
}
