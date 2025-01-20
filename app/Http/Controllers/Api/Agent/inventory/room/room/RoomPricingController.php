<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\inventory\room\room\RoomPricingRequest;

use App\Models\RoomPricing;
use App\Models\CurrencyAgent;
use App\Models\RoomPricingData;

class RoomPricingController extends Controller
{
    public function __construct(private RoomPricing $pricing,
    private CurrencyAgent $currency, private RoomPricingData $pricing_data){}

    public function view(Request $request){
        $validation = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
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
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        } 
        $currencies = $this->currency
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get();
        $pricing_data = $this->pricing_data
        ->get();
        $pricing = $this->pricing
        ->where('room_id', $request->room_id)
        ->with('currency', 'pricing_data')
        ->get();

        return response()->json([
            'currencies' => $currencies,
            'pricing_data' => $pricing_data,
            'pricing' => $pricing,
        ]);
    }

    public function pricing($id){
        $pricing = $this->pricing
        ->where('id', $id)
        ->with('currency', 'pricing_data')
        ->get();

        return response()->json([
            'pricing' => $pricing,
        ]);
    }

    public function duplicate(Request $request){
        
    }

    public function create(RoomPricingRequest $request){
        $room_pricing = $request->validated();
        $pricing = $this->pricing
        ->create($room_pricing);

        return response()->json([
            'success' => $pricing
        ]);
    }

    public function modify(RoomPricingRequest $request, $id){
        $room_pricing = $request->validated();
        $pricing = $this->pricing
        ->where('room_id', $request->room_id)
        ->where('id', $id)
        ->first();
        $pricing->update($room_pricing);

        return response()->json([
            'success' => $pricing
        ]);
    }

    public function delete($id){
        $this->pricing
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
