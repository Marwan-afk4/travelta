<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStage extends Model
{
    protected $fillable = [
        'request_booking_id',
        'stages',
        'action',
        'priority',
        'follow_up_date',
        'result',
        'send_by',
    ];
}
