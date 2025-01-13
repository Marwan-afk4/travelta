<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinantiolAcounting extends Model
{
    protected $fillable = [
        'name',
        'details',
        'balance',
        'currency_id',
        'affilate_id',
        'agent_id',
        'status',
        'logo',
    ];
    protected $appends = ['logo_link'];

    public function getLogoLinkAttribute(){
        return url('storage/' . $this->attributes['logo']);
    }

    public function currancy(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
}
