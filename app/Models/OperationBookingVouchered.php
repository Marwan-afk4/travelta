<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationBookingVouchered extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'booking_engine_id',
        'confirmation_num',
        'totally_paid',
        'name',
        'phone',
        'email',
    ];
}
