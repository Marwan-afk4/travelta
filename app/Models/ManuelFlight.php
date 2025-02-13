<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelFlight extends Model
{
    protected $fillable = [
        'type',
        'direction',
        'from_to',
        'departure',
        'arrival',
        'class',
        'adults',
        'childreen',
        'infants',
        'airline',
        'ticket_number',
        'adult_price',
        'child_price',
        'ref_pnr',
        'manuel_booking_id',
    ];

    public function getfromToAttribute($data){
        try {
            if (is_string($data)) {
                return json_decode($data);
            }
            else{
                return $data;
            }
        } catch (\Throwable $th) {
            return $data;
        }
    }
}
