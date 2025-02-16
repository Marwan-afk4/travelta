<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'supplier_id',
        'amount',
        'type',
        'date',
    ];
}
