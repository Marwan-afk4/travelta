<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\inventory\room\room\RoomRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\Room;
use App\Models\Supplement;
use App\Models\RoomAgency;
use App\Models\RoomCancel;

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
        // description, status, price_type => [fixed, variable], price, quantity, max_adults, 
        // max_children, max_capacity, min_stay, room_type_id, hotel_id, hotel_meal_id, 
        // currency_id, b2c_markup, b2e_markup, b2b_markup, tax_type => [include, exclude, include_except], 
        // check_in, check_out, policy, children_policy, cancelation => [free, non_refunable]
        // supplements[name, type => [night, stay, person], price, currency_id]
        // amenities[]
        // agencies[agency_code, percentage]
        // taxes[]
        // except_taxes[]
        // free_cancelation[amount, type => [precentage, value], before]
        // Json => {"status": "1","price_type": "fixed","price": "100","quantity": "3","description": "Description","max_adults": "3","max_children": "2","max_capacity": "5","min_stay": "2","room_type_id": "1","hotel_id": "1","hotel_meal_id": "1","currency_id": "2","b2c_markup": "10","b2e_markup": "20","b2b_markup": "30","tax_type": "include_except","check_in": "11:00Am","check_out": "11:00Am","policy": "My policies","children_policy": "childreen policies","cancelation": "free","supplements": "[{\"name\":\"Chocolate\",\"type\":\"night\",\"price\":12,\"currency_id\":2}]","amenities": ["1"],"agencies": "[{\"agency_code\":\"1234\",\"percentage\":10}]","taxes": ["1"],"free_cancelation": "[{\"amount\":100,\"type\":\"value\",\"before\":2}]",                        "except_taxes": ["1"],"thumbnail": {}}
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
        try {
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
                'success' => $request->all()
            ]);
        } 
        catch (\Throwable $th) {
            $room->delete();
            return response()->json([
                'errors' => 'Something error'
            ], 400);
        }
    }

    public function modify(){

    }

    public function delete(){

    }
}
