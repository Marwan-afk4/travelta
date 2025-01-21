<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\inventory\room\room\RoomAvailabilityRequest;

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

    public function create(RoomAvailabilityRequest $request){
        $roomRequest = $request->validated();
        foreach ($request->rooms as $item) {
            $this->room_availability
            ->create([
                'room_id' => $request->room_id,
                'from' => $item['from'],
                'to' => $item['to'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(RoomAvailabilityRequest $request, $id){
        $roomRequest = $request->validated();
        foreach ($request->rooms as $item) {
            $this->room_availability
            ->create([
                'room_id' => $request->room_id,
                'from' => $item['from'],
                'to' => $item['to'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function delete($id){
        
    }
}
