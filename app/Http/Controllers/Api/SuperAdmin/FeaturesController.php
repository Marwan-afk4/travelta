<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeaturesController extends Controller
{
    protected $UpdateFeature =['name', 'description', 'image'];

    public function getAllFeatures(){
        $features = Feature::all();
        $data = [
            'features' => $features
        ];
        return response()->json($data);
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
        $feature = Feature::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
        ]);
        return response()->json([
            'message' => 'Feature added successfully',
        ]);
    }

    public function updateFeature(Request $request,$id){
        $feature = Feature::find($id);
        $feature->update($request->only($this->UpdateFeature));
        return response()->json([
            'message' => 'Feature updated successfully',
        ]);
    }

    public function deleteFeature($id){
        $feature = Feature::find($id);
        $feature->delete();
        return response()->json([
            'message' => 'Feature deleted successfully',
        ]);
    }
}
