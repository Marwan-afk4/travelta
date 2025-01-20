<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RoomPricingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomDataController extends Controller
{
    protected $updateRoomData = ['room_type', 'meal_plan', 'adults', 'children'];

    public function getRoomData(){
        $roomData = RoomPricingData::all();
        return response()->json([
            'rooms_data'=>$roomData
        ]);

    }

    public function addRoomData(Request $request){
        $validation = Validator::make($request->all(), [
            'room_type' => 'required|in:double,single,triple,quadrant',
            'meal_plan' => 'required|in:bed,bed_breakfast,half_board,full_board,all_inclusive',
            'adults'=>'required|integer',
            'children'=>'required|integer',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $roomData = RoomPricingData::create([
            'room_type'=>$request->room_type,
            'meal_plan'=>$request->meal_plan,
            'adults'=>$request->adults,
            'children'=>$request->children
        ]);
        return response()->json([
            'message' => 'Room data added successfully',
        ]);
    }

    public function deleteRoomData($id){
        $roomData=RoomPricingData::find($id);
        $roomData->delete();
        return response()->json([
            'message' => 'Room data deleted successfully',
        ]);
    }

    public function updateRoomData(Request $request,$id){
        $roomData = RoomPricingData::find($id);
        $roomData->update($request->only($this->updateRoomData));
        return response()->json([
            'message' => 'Room data updated successfully',
        ]);
    }
}
