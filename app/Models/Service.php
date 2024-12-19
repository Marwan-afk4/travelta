<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'description'
    ];

    public function suppliers(){
        return $this->belongsToMany(SupplierAgent::class, 'supplier_agent_service', 'service_id', 'supplier_agent_id');
    }
}
