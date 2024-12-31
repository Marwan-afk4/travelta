<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable =[
        'name',
        'phone',
        'email',
        'gender',
        'emergency_phone',
        'password',
    ];
    protected $appends = ['role'];

    public function getRoleAttribute(){
        return 'user';
    }

    protected $hidden = [
        'password',
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
