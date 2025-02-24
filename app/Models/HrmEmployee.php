<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrmEmployee extends Model
{ 
    protected $fillable =[
        'affilate_id',
        'agent_id',
        'name',
        'department_id',
        'role_id',
        'image',
        'user_name',
        'password',
        'address',
        'phone',
        'email',
        'status',
    ];

    public function department(){
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }
}
