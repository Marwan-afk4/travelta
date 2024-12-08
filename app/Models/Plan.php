<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{


    protected $fillable = [
        'name',
        'description',
        'user_limit',
        'branch_limit',
        'period_in_days',
        'module_type',
        'price',
        'discount_type',
        'price_after_discount',
        'admin_cost',
        'branch_cost',
    ];
}
