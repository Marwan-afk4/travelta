<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\inventory\room\room\RoomRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Supplement;

class CreateRoomController extends Controller
{
    protected $roomRequest = [
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
    ];

    public function create(RoomRequest $request){

    }

    public function modify(){

    }

    public function delete(){

    }
}
