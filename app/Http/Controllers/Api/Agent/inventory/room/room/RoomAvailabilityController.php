<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\RoomAvailability;

class RoomAvailabilityController extends Controller
{
    public function __construct(private RoomAvailability $room_availability){}

    public function view(Request $request){
        $validation = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $room_availability = $this->room_availability
        ->where('room_id', $request->room_id)
        ->get();

        return response()->json([
            'room_availability' => $room_availability
        ]);
    }

    public function room_availability($id){
        
    }

    public function create(){
        
    }

    public function modify(){
        
    }

    public function delete(){
        
    }
}
