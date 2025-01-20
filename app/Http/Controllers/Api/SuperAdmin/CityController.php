<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    protected $updateCity = ['name', 'country_id'];

    public function getCity(){
        $city = City::all();
        $data = [
            'city' => $city
        ];
        return response()->json($data);
    }

    public function addCity(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:cities,name',
            'country_id' => 'required|exists:countries,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $city = City::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
        ]);
        return response()->json([
            'message' => 'City added successfully',
        ]);
    }

    public function updateCity(Request $request, $id){
        $city=City::find($id);
        $city->update($request->only($this->updateCity));
        return response()->json([
            'message' => 'City updated successfully',
        ]);
    }

    public function deleteCity($id){
        $city=City::find($id);
        $city->delete();
        return response()->json([
            'message' => 'City deleted successfully',
        ]);
    }
}
