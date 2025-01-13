<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\RoomType;
use App\Models\Hotel;
use App\Models\HotelMeal;
use App\Models\CurrencyAgent;
use App\Models\RoomAmenity;
use App\Models\CountryTax;

class RoomController extends Controller
{
    public function __construct(private RoomType $room_types, private Hotel $hotels,
    private HotelMeal $meal_plans, private CurrencyAgent $currencies, 
    private RoomAmenity $room_amenities, private CountryTax $country_taxes){}

    public function lists(Request $request){
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
        $hotels = $this->hotels
        ->get();
        $room_types = $this->room_types
        ->where($role, $agent_id)
        ->where('status', 1)
        ->get();
        $currencies = $this->currencies
        ->where($role, $agent_id)
        ->get();
        $room_amenities = $this->room_amenities
        ->where($role, $agent_id)
        ->where('status', 1)
        ->get();

        return response()->json([
            'hotels' => $hotels,
            'room_types' => $room_types,
            'currencies' => $currencies,
            'room_amenities' => $room_amenities,
        ]);
    }

    public function hotel_lists(Request $request){
        $validation = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $hotel = $this->hotels
        ->where('id', $request->hotel_id)
        ->first();
        $country_id = $hotel->country_id;
        $meal_plans = $this->meal_plans
        ->where('hotel_id', $request->hotel_id)
        ->get();
        $country_taxes = $this->country_taxes
        ->where('country_id', $country_id)
        ->get();

        return response()->json([
            'meal_plans' => $meal_plans,
            'country_taxes' => $country_taxes,
        ]);
    }
}
