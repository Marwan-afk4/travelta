<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    protected $updateHotel = ['hotel_name', 'country_id', 'city_id', 'zone_id', 'email', 'phone_number', 'rating', 'image'];

    public function Hotels(){
        $hotels = Hotel::all();
        $data = [
            'hotels' => $hotels
        ];
        return response()->json($data);
    }

    public function getCountries(){
        $Countrys = Country::all();
        $Citys = City::all();
        $zones = Zone::all();
        $data = [
            'Countrys' => $Countrys,
            'Citys' => $Citys,
            'zones' => $zones
        ];
        return response()->json($data);
    }

    public function AddHotel(Request $request){
        $validation = Validator::make($request->all(), [
            'hotel_name' => 'required',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'required|exists:zones,id',
            'email' => 'required|email|unique:hotels,email',
            'phone_number' => 'required|unique:hotels,phone_number',
            'rating' => 'nullable',
            'image' => 'nullable|array',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $hotel = Hotel::create([
            'hotel_name' => $request->hotel_name,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'zone_id' => $request->zone_id,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'rating' => $request->rating,
            'image' => $request->image,
        ]);
        return response()->json([
            'message' => 'Hotel added successfully',
        ]);
    }

    public function DeleteHotel($id){
        $hotel=Hotel::find($id);
        $hotel->delete();
        return response()->json([
            'message' => 'Hotel deleted successfully',
        ]);
    }
 public function UpdateHotel(Request $request,$id){
        $hotel=Hotel::find($id);
        $hotel->update($request->only($this->updateHotel));
        return response()->json([
            'message' => 'Hotel updated successfully',
        ]);
    }

}
