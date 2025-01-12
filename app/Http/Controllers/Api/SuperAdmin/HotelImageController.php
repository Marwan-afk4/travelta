<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HotelIamge;
use Illuminate\Http\Request;

class HotelImageController extends Controller
{

    public function getAllHotelImages($hotel_id){
        $images = HotelIamge::where('hotel_id', $hotel_id)->get();
        $data = [
            'hotel_images' => $images
        ];
        return response()->json($data);
    }

    public function deleteHotelImage($id){
        $image = HotelIamge::find($id);
        $image->delete();
        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }


}
