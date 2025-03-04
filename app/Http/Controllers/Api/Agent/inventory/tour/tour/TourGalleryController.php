<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use Illuminate\Support\Facades\Validator;

use App\Models\TourImage;

class TourGalleryController extends Controller
{
    public function __construct(private TourImage $tour_image){}
    use image;

    public function gallery($id){
        // tour/gallery/{id}
        $images = $this->tour_image
        ->where('tour_id', $id)
        ->get();

        return response()->json([
            'images' => $images
        ]);
    }

    public function add_gallery(Request $request){
        // tour/add_gallery
        // Keys
        // images[], status, tour_id
        $validation = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'images' => 'required|array', 
            'status' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $images = $request->images;
        foreach ($images as $item) {
            $image = $this->storeBase64Image($item, 'agent/inventory/tour/gallery');
            $this->tour_image
            ->create([
                'tour_id' => $request->tour_id,
                'image' => $image,
                'status' => $request->status,
            ]);
        }

        return response()->json([
            'success' => 'You add images success'
        ]);
    }

    public function delete($id){
        // tour/delete_gallery/{id}
        $image = $this->tour_image
        ->where('id', $id)
        ->first();
        $this->deleteImage($image->image);
        $image->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
