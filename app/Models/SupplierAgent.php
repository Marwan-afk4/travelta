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
        'emails',
        'phones',
        'affilate_id',
        'agent_id',
        'emergency_phone',
    ];
    protected $appends = ['name'];

    public function getNameAttribute(){
        return $this->attributes['agent'];
    }

    public function services(){
        return $this->belongsToMany(Service::class, 'supplier_agent_service', 'supplier_agent_id', 'service_id')
        ->withPivot('description');
    }
    
    public function legal_papers(){
        return $this->hasMany(LegalPaper::class, 'supplier_agent_id');
    }

    public function getemailsAttribute($data){
        try {
            return json_decode($data);
        } catch (\Throwable $th) {
            return $data;
        }
    }

    public function getphonesAttribute($data){
        try {
            return json_decode($data);
        } catch (\Throwable $th) {
            return $data;
        }
    }
}
