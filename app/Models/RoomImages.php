<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomImages extends Model
{
    protected $fillable = [
        'room_id',
        'thumbnail',
        'status',
    ];
    protected $appends = ['thumbnail_link'];

    public function getThumbnailLinkAttribute(){
        if (isset($this->attributes['thumbnail'])) {
            return url('storage/' . $this->attributes['thumbnail']);
        }
        return null; 
    }
}
