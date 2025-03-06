<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierAgentService extends Model
{
    protected $table = 'supplier_agent_service';

    protected $fillable = [
        'supplier_agent_id',
        'service_id',
        'description',
    ];
}
