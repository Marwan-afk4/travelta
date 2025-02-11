<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationBookingCanceled extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'booking_engine_id',
        'cancelation_reason',
    ];
}
