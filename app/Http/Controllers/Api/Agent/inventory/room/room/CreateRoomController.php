<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\inventory\room\room\RoomRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Supplement;

class CreateRoomController extends Controller
{
    public function __construct(private Room $room, private Supplement $supplements){}
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
        // description, status, price_type, price, quantity, max_adults, 
        // max_children, max_capacity, min_stay, room_type_id, hotel_id, hotel_meal_id, 
        // currency_id, b2c_markup, b2e_markup, b2b_markup, tax_type, check_in, check_out, 
        // policy, children_policy, cancelation
        // supplements[name, type, price, currency_id]
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $roomRequest = $request->only($this->roomRequest);
        $roomRequest[$agent_type] = $agent_id;
        $room = $this->room
        ->create($roomRequest);
        // Add Supplements
        $supplements = is_string($request->supplements) ? json_decode($request->supplements): 
        $request->supplements;
        foreach ($supplements as $item) {
            $this->supplements
            ->create([
                'room_id' => $room->id,
                'name' => $item->name,
                'type' => $item->type,
                'price' => $item->price,
                'currency_id' => $item->currency_id,
            ]);
        }
        // Add Amenity
    }

    public function modify(){

    }

    public function delete(){

    }
}
