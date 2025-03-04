<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierBalance extends Model
{
    protected $fillable = [
        'supplier_id',
        'currency_id',
        'balance',
    ];

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
