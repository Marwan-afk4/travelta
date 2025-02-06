<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationBookingConfirmed extends Model
{
    protected $fillable = [
        'manuel_booking_id',
        'comfirmed',
        'deposits',
    ];
}
