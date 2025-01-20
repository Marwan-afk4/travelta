<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use Illuminate\Support\Facades\Validator;

use App\Models\RoomImages;

class RoomGalleryController extends Controller
{
    public function __construct(private RoomImages $room_image){}
    use image;

    public function gallery($id){
        $images = $this->room_image
        ->select('id', 'thumbnail')
        ->where('status', true)
        ->where('room_id', $id)
        ->get();

        return response()->json([
            'images' => $images
        ]);
    }

    public function add_gallery(Request $request){
        // Keys
        // images[], status, room_id
        $validation = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'images' => 'required', 
            'status' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $images = is_string($request->images) ? json_decode($request->images): $request->images;
        foreach ($images as $item) {
            $image = $this->storeBase64Image($item, 'agent/inventory/room/gallery');
            $this->room_image
            ->create([
                'room_id' => $request->room_id,
                'thumbnail' => $image,
                'status' => $request->status,
            ]);
        }

        return response()->json([
            'success' => 'You add images success'
        ]);
    }

    public function delete($id){
        $image = $this->room_image
        ->where('id', $id)
        ->first();
        $this->deleteImage($image->thumbnail);
        $image->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
