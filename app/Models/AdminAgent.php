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
        'image',
    ];
    protected $appends = ['image_link'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getImageLinkAttribute(){
        if(!empty($this->image)){
            return url('storage/' . $this->image);
        }
        return null;
    }

    public function agent(){
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function affilate(){
        return $this->belongsTo(AffilateAgent::class, 'affilate_id');
    }

    public function position(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }

    public function user_positions(){
        return $this->belongsTo(AdminAgentPosition::class, 'position_id');
    }
}
