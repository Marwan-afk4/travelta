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
use App\Models\RoomAgency;
use App\Models\CountryTax;
use App\Models\Room;
use App\Models\Supplement;
use App\Models\RoomImages;
use App\Models\RoomCancel;

class RoomController extends Controller
{
    public function __construct(private RoomType $room_types, private Hotel $hotels,
    private HotelMeal $meal_plans, private CurrencyAgent $currencies, 
    private RoomAmenity $room_amenities, private CountryTax $country_taxes,
    private Room $room, private RoomAgency $agency, private Supplement $supplement,
    private RoomImages $room_images, private RoomCancel $room_cancel){}

    // public function view(Request $request){
    //     if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
    //         $agent_id = $request->user()->affilate_id;
    //     }
    //     elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
    //         $agent_id = $request->user()->agent_id;
    //     }
    //     else{
    //         $agent_id = $request->user()->id;
    //     }
    //     if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
    //         $role = 'affilate_id';
    //     }
    //     else{
    //         $role = 'agent_id';
    //     }
    //     $rooms = $this->room
    //     ->select('quantity', 'price')
    //     ->where($role, $agent_id)
    //     ->get();
    // }

    public function status(Request $request, $id){
        // room/status/{id}
        $validation = Validator::make($request->all(), [
            'status' => 'required|boolean',
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
        $this->room
        ->where($role, $agent_id)
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned', 
        ]);
    }

    public function accepted(Request $request, $id){
        // room/accepted/{id}
        $validation = Validator::make($request->all(), [
            'accepted' => 'required|boolean',
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
        $this->room
        ->where($role, $agent_id)
        ->where('id', $id)
        ->update([
            'accepted' => $request->accepted
        ]);

        return response()->json([
            'success' => $request->accepted ? 'active' : 'banned', 
        ]);
    }

    public function lists(Request $request){
        // room/lists
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
        ->select('id', 'name')
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
        // room/hotel_lists
        // Keys
        // hotel_id
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

    public function room_list(Request $request){
        // room/room_list
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
        $room = $this->room
        ->with('amenity', 'agencies', 'supplement', 'taxes', 'except_taxes', 'free_cancelation')
        ->where($agent_type, $agent_id)
        ->get();

        return response()->json([
            'room' => $room
        ]);
    }

    public function room(Request $request, $id){
        // room/item/{id}
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
        $room = $this->room
        ->with('amenity', 'agencies', 'supplement', 'taxes', 'except_taxes', 'free_cancelation')
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->first();

        return response()->json([
            'room' => $room
        ]);
    }

    public function duplicate_room(Request $request, $id){
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
        $room = $this->room
       // ->with('amenity', 'agencies', 'supplement', 'taxes', 'except_taxes', 'free_cancelation')
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->first();
        if (empty($room)) {
            return response()->json([
                'errors' => 'id is wrong'
            ], 400);
        }
        $new_room = $this->room
        ->create($room->toArray());
        if (!empty($room->amenity)) {
            $room_amenities_data = $room->amenity->pluck('id')->toArray();
            $new_room->amenity()->attach($room_amenities_data);
        }
        if (!empty($room->agencies)) {
            $room_agency_data = $room->agencies;
            foreach ($room_agency_data as $item) {
                $item->room_id = $new_room->id;
                $this->agency
                ->create($item->toArray());
            }
        }
        if (!empty($room->supplement)) {
            $room_supplement_data = $room->supplement;
            foreach ($room_supplement_data as $item) {
                $item->room_id = $new_room->id;
                $this->supplement
                ->create($item->toArray());
            }
        }
        if (!empty($room->taxes)) {
            $room_taxes_data = $room->taxes->pluck('id')->toArray();
            $new_room->taxes()->attach($room_taxes_data);
        }
        if (!empty($room->except_taxes)) {
            $room_except_taxes_data = $room->except_taxes->pluck('id')->toArray();
            $new_room->except_taxes()->attach($room_except_taxes_data);
        }
        if (!empty($room->free_cancelation)) {
            $room_free_cancelation_data = $room->free_cancelation;
            foreach ($room_free_cancelation_data as $item) {
                $item->room_id = $new_room->id;
                $this->room_cancel
                ->create($item->toArray());
            }
        }
        if (!empty($room->gallery)) {
            $room_gallery_data = $room->gallery;
            foreach ($room_gallery_data as $item) {
                $item->room_id = $new_room->id;
                $this->room_images
                ->create($item->toArray());
            }
        }

        return response()->json([
            'success' => $room
        ]);
    }
}
