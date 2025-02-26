<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminAgent extends Model
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $fillable = [
        'affilate_id',
        'agent_id',
        'position_id',
        'role',
        'name',
        'email',
        'phone',
        'password',
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

    public function position(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }

    public function user_positions(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }
}
