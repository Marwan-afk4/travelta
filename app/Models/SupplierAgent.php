<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierAgent extends Model
{
    protected $fillable = [
        'agent',
        'admin_name',
        'admin_phone',
        'admin_email',
    ];
}
