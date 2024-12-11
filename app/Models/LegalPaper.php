<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPaper extends Model
{
    protected $fillable = [
        'image',
        'type',
        'agent_id',
        'affilate_id',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
