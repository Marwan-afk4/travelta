<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourImage extends Model
{
    protected $fillable = [
        'tour_id',
        'image',
        'status',
    ];
}
