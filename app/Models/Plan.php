<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

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
        'type',
        'discount_value'
    ];

    public function agents(){
        return $this->hasMany(Agent::class);
    }
}
