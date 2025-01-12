<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\RoomType;

class RoomTypesController extends Controller
{
    public function __construct(private RoomType $room_types){}
    protected $roomRequest = [
        'name',
        'status',
    ];

    public function view(Request $request){
        // /agent/room/settings/types
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
        $room_types = $this->room_types 
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'room_types' => $room_types, 
        ]);
    }

    public function status(Request $request, $id){
        // /agent/room/settings/types/status/{id}
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
        $room_types = $this->room_types 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]); 

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function room_type(Request $request, $id){
        // /agent/room/settings/types/item/{id}
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
        $room_types = $this->room_types 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();

        return response()->json([
            'room_types' => $room_types,
        ]);
    }

    public function create(Request $request){
        // /agent/room/settings/types/add
        // Keys
        // name, status
        $validation = Validator::make($request->all(), [
            'name' => 'required',
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
        $roomRequest = $request->only($this->roomRequest);
        $roomRequest[$role] = $agent_id;
        $room_types = $this->room_types
        ->create($roomRequest);

        return response()->json([
            'success' => $room_types,
        ]);
    }

    public function modify(Request $request, $id){
        // /agent/room/settings/types/update/{id}
        // Keys
        // name, status
        $validation = Validator::make($request->all(), [
            'name' => 'required',
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
        $roomRequest = $request->only($this->roomRequest);
        $roomRequest[$role] = $agent_id;
        $room_types = $this->room_types
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $room_types->update($roomRequest);

        return response()->json([
            'success' => $room_types,
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/room/settings/types/delete/{id}
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
        $this->room_types
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete room types success'
        ], 200);
    }
}
