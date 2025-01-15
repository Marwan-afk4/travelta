<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\inventory\room\room\RoomRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Supplement;

class CreateRoomController extends Controller
{
    public function __construct(private Room $room, private Supplement $supplements,
    private RoomAgency $room_agency, private RoomCancel $room_cancelation){}
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
        // amenities[]
        // agencies[agency_code, percentage]
        // taxes[]
        // free_cancelation[amount, type, before]
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
        if ($request->amenities) {
            $amenities = is_string($request->amenities)? json_decode($request->amenities)
            :$request->amenities;
            $room->amenity()->attach($amenities);
        }
        // Add Agencies
        if ($request->agencies) {
            $agencies = is_string($request->agencies) ? json_decode($request->agencies): 
            $request->agencies; 
            foreach ($agencies as $item) {
                $this->room_agency
                ->create([
                    'room_id' => $room->id,
                    'percentage' => $item->percentage,
                    'agency_code' => $item->agency_code,
                ]);
            }
        }
        // Add Taxes 
        if ($request->taxes) {
            $taxes = is_string($request->taxes) ? json_decode($request->taxes): 
            $request->taxes; 
            $room->taxes()->attach($taxes);
        }
         // Add Except Taxes
        if ($request->except_taxes) {
        $except_taxes = is_string($request->except_taxes) ? json_decode($request->except_taxes): 
        $request->except_taxes; 
        $room->except_taxes()->attach($except_taxes);
        }
        // Add free cancelation
        if ($request->free_cancelation) {
            $free_cancelation = is_string($request->free_cancelation) ? json_decode($request->free_cancelation): 
            $request->free_cancelation;
            foreach ($free_cancelation as $item) {
                $this->room_cancelation
                ->create([
                    'room_id' => $room->id,
                    'amount' => $item->amount,
                    'type' => $item->type,
                    'before' => $item->before,
                ]);
            }
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(){

    }

    public function delete(){

    }
}
