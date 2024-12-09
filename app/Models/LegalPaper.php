<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPaper extends Model
{
    protected $fillable = [
        'image',
        'agent_id',
        'user_id',
    ];
}
