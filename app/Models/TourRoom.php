<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourRoom extends Model
{
    protected $fillable = [
        'tour_id',
        'adult_single',
        'adult_double',
        'adult_triple',
        'adult_quadruple', 
        'children_single',
        'children_double',
        'children_triple',
        'children_quadruple', 
    ];
}
