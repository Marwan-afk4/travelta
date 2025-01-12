<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\inventory\room\settings\RoomAmenityRequest;

use App\Models\RoomAmenity;

class RoomAmenityController extends Controller
{
    public function __construct(private RoomAmenity $room_amenity){}
    protected $roomRequest = [
        'name',
        'selected',
        'status',
    ];
    public function view(Request $request){
        // /agent/room/settings/amenity
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
        else {
            $role = 'agent_id';
        }
        $room_amenity = $this->room_amenity 
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'room_amenity' => $room_amenity, 
        ]);
    }

    public function status(Request $request, $id){
        // /agent/room/settings/amenity/status/{id}
        // Keys
        // status
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
        else {
            $role = 'agent_id';
        } 
        $room_amenity = $this->room_amenity
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function room_amenity(Request $request, $id){
        // /agent/room/settings/amenity/item/{id}
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
        else {
            $role = 'agent_id';
        } 
        $room_amenity = $this->room_amenity 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();

        return response()->json([
            'room_amenity' => $room_amenity,
        ]);
    }

    public function create(RoomAmenityRequest $request){
        // /agent/room/settings/amenity/add
        // Keys
        // name, selected, status
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
        else {
            $role = 'agent_id';
        }
        $roomRequest = $request->validated();
        $roomRequest[$role] = $agent_id;
        $room_amenity = $this->room_amenity
        ->create($roomRequest);

        return response()->json([
            'success' => $room_amenity,
        ]);
    }

    public function modify(RoomAmenityRequest $request, $id){
        // /agent/room/settings/amenity/update/{id}
        // Keys
        // name, selected, status
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
        else {
            $role = 'agent_id';
        }
        $roomRequest = $request->validated();
        $roomRequest[$role] = $agent_id;
        $room_amenity = $this->room_amenity
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $room_amenity->update($roomRequest);

        return response()->json([
            'success' => $room_amenity,
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/room/settings/amenity/delete/{id}
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
        else { 
            $role = 'agent_id';
        }
        $this->room_amenity
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete room types success'
        ], 200);
    }
}
