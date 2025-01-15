<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomExtra extends Model
{
    protected $fillable = [
        'name',
        'thumbnail',
        'price',
        'status',
        'hotel_id',
        'affilate_id',
        'agent_id',
    ];
    protected $appends = ['thumbnail_link'];

    public function getThumbnailLinkAttribute(){
        return url('storage/' . $this->attributes['thumbnail']);
    }
    public function hotel(){
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
