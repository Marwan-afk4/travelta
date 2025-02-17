<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpensesCategory extends Model
{
    protected $fillable = [
        'affilate_id',
        'agent_id',
        'category_id',
        'name', 
    ];

    public function parent_category(){
        return $this->belongsTo(ExpensesCategory::class, 'category_id');
    }
}
