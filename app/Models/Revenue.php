<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'category_id',
        'financiale_id',
        'currency_id',
        'title',
        'date',
        'amount',
        'description',
    ];

    public function category(){
        return $this->belongsTo(RevenueCategory::class, 'category_id');
    }

    public function financial(){
        return $this->belongsTo(FinantiolAcounting::class, 'financiale_id');
    }

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
