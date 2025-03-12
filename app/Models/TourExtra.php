<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourExtra extends Model
{
    protected $fillable = [
        'tour_id',
        'name',
        'price',
        'currency_id',
        'type',
    ];

    public function currency()
    {
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function book_tour_extra(){
        return $this->hasMany(BookTourExtra::class, 'tour_extra_id');
    }
}
