<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    protected $updateZone = ['name', 'country_id', 'city_id'];

    public function getZones(){
        $zone = Zone::all();
        $data = [
            'zone' => $zone
        ];
        return response()->json($data);
    }

    public function addZone(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required|unique:zones,name',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $zone = Zone::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
        ]);
        return response()->json([
            'message' => 'Zone added successfully',
        ]);
    }

    public function updateZone(Request $request, $id){
        $zone=Zone::find($id);
        $zone->update($request->only($this->updateZone));
        return response()->json([
            'message' => 'Zone updated successfully',
        ]);

    }

    public function deleteZone($id){
        $zone=Zone::find($id);
        $zone->delete();
        return response()->json([
            'message' => 'Zone deleted successfully',
        ]);
    }
}
