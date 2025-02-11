<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAmenity extends Model
{
    protected $fillable = [
        'name',
        'selected',
        'status',
        'logo',
        'affilate_id',
        'agent_id',
    ];
    protected $appends = ['logo_link'];

    public function getLogoLinkAttribute(){
        return url('storage/' . $this->attributes['logo']);
    }
}
