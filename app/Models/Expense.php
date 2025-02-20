<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'category_id',
        'financial_id',
        'currency_id',
        'title',
        'date',
        'amount',
        'description',
    ];

    public function category(){
        return $this->belongsTo(ExpensesCategory::class, 'category_id');
    }

    public function financial(){
        return $this->belongsTo(FinantiolAcounting::class, 'financial_id');
    }

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
