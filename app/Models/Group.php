<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'affilate_id',
        'agent_id',
    ];

    public function nationalities(){
        return $this->belongsToMany(Nationality::class, 'nationality_group', 'group_id', 'nationality_id');
    }
}
