<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\inventory\room\settings\ExtraRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\RoomExtra;
use App\Models\Hotel;

class RoomExtraController extends Controller
{
    use image;
    public function __construct(private RoomExtra $room_extra, private Hotel $hotels){}

    public function view(Request $request){
        // /agent/room/settings/extra
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
        $room_extra = $this->room_extra 
        ->where($role, $agent_id)
        ->with('hotel')
        ->get();
        $hotels = $this->hotels
        ->get();

        return response()->json([
            'room_extra' => $room_extra,
            'hotels' => $hotels,
        ]);
    }

    public function status(Request $request, $id){
        // /agent/room/settings/extra/status/{id}
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
        $room_extra = $this->room_extra
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }
    
    public function room_extra(Request $request, $id){
        // /agent/room/settings/extra/item/{id}
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
        $room_extra = $this->room_extra 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();

        return response()->json([
            'room_extra' => $room_extra,
        ]);
    }

    public function create(ExtraRequest $request){
        // /agent/room/settings/extra/add
        // Keys
        // name, thumbnail, price, hotel_id, status
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
        if (!is_string($request->thumbnail)) {
            $image_path = $this->upload($request, 'thumbnail', 'admin/inventory/room/extra');
            $roomRequest['thumbnail'] = $image_path;
        }
        $roomRequest[$role] = $agent_id;
        $room_extra = $this->room_extra
        ->create($roomRequest);

        return response()->json([
            'success' => $room_extra,
        ]);
    }

    public function modify(ExtraRequest $request, $id){
        // /agent/room/settings/extra/update/{id}
        // Keys
        // name, thumbnail, price, hotel_id, status
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
        $room_extra = $this->room_extra
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();   
        if (!is_string($request->thumbnail)) {
            $image_path = $this->update_image($request, $room_extra->thumbnail, 'thumbnail', 'admin/inventory/room/extra');
            $roomRequest['thumbnail'] = $image_path;
        }
        else{
            $roomRequest->except('thumbnail');
        }
        $room_extra->update($roomRequest);

        return response()->json([
            'success' => $room_extra,
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/room/settings/extra/delete/{id}
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
        $room_extra = $this->room_extra
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $image_path = $this->deleteImage($room_extra->thumbnail);
       
        $room_extra->delete();

        return response()->json([
            'success' => 'You delete room extra success'
        ], 200);
    }
}
