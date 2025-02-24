<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrmEmployee extends Model
{ 
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable =[
        'affilate_id',
        'agent_id',
        'name',
        'agent',
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

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function department(){
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }
}
