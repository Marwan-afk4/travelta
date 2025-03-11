<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookTourExtra extends Model
{


    protected $fillable =[
        'book_tour_id',
        'extra_id',
        'extra_count',
    ];

    public function book_tour(){
        return $this->belongsTo(BookTourengine::class, 'book_tour_id');
    }

    public function extra(){
        return $this->belongsTo(TourExtra::class);
    }
}
