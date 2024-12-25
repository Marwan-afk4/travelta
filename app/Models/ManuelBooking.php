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
        'price',
        'currency_id',
        'tax_type',
        'total_price',
        'country_id',
        'city_id',
        'mark_up',
        'mark_up_type',
    ];

    public function taxes(){
        return $this->belongsToMany(Tax::class, 'manuel_taxes', 'manuel_id', 'tax_id');
    }
}
