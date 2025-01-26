<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'agent_id',
        'affilate_id',
        'description',
        'status',
        'price_type',
        'price',
        'quantity',
        'max_adults',
        'max_children',
        'max_capacity',
        'min_stay',
        'room_type_id',
        'hotel_id',
        'hotel_meal_id',
        'currency_id',
        'b2c_markup',
        'b2e_markup',
        'b2b_markup',
        'tax_type',
        'check_in',
        'check_out',
        'policy',
        'children_policy',
        'cancelation',
        'thumbnail',
        'accepted',
    ];
    protected $appends = ['thumbnail_link'];

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }
    
    public function room_type(){
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function hotel_meal(){
        return $this->belongsTo(HotelMeal::class, 'hotel_meal_id');
    }

    public function getThumbnailLinkAttribute(){
        return url('storage/' . $this->attributes['thumbnail']);
    }

    public function amenity(){
        return $this->belongsToMany(RoomAmenity::class, 'amenities_room', 'room_id', 'amenity_id');
    }

    public function agencies(){
        return $this->hasMany(RoomAgency::class, 'room_id');
    }

    public function supplement(){
        return $this->hasMany(Supplement::class, 'room_id');
    }

    public function taxes(){
        return $this->belongsToMany(CountryTax::class, 'room_tax', 'room_id', 'tax_id');
    }

    public function except_taxes(){
        return $this->belongsToMany(CountryTax::class, 'room_except_tax', 'room_id', 'tax_id');
    }

    public function free_cancelation(){
        return $this->hasMany(RoomCancel::class, 'room_id');
    }

    public function gallery(){
        return $this->hasMany(RoomImages::class, 'room_id');
    }
}
