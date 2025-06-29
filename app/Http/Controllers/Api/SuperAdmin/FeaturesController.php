<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeaturesController extends Controller
{
    use image;
    protected $UpdateFeature =['name', 'description', 'image'];

    public function getAllFeatures()
{
    $features = Feature::all();
    $data = $features->map(function ($feature) {
        return [
            'id' => $feature->id,
            'name' => $feature->name,
            'description' => $feature->description,
            'image_url' => $feature->image ? url('storage/' . $feature->image) : null, 
        ];
    });
    return response()->json(['features' => $data]);
}


    public function addFeature(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:features,name',
            'description' => 'nullable',
            'image' => 'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $image_path =null;
        if ($request->has('image')) {
            $image_path =$this->storeBase64Image($request->image, 'admin/feature/image');
        }
        $feature = Feature::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
        ]);
        $imageLink = $image_path ? url('storage/' . $image_path) : null;


        return response()->json([
            'message' => 'Feature added successfully',
        ]);
    }

    public function updateFeature(Request $request,$id){
        $feature = Feature::find($id);
        $featureRequest = $request->only($this->UpdateFeature);
        if (!empty($request->image)) {
            $featureRequest['image'] = $this->storeBase64Image($request->image, 'admin/feature/image');
            $this->deleteImage($feature->image);
        }
        
        $feature->update($featureRequest);
        return response()->json([
            'message' => 'Feature updated successfully',
        ]);
    }

    public function deleteFeature($id){
        $feature = Feature::find($id);
        $this->deleteImage($feature->image);
        $feature->delete();
        return response()->json([
            'message' => 'Feature deleted successfully',
        ]);
    }
}
