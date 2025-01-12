<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilitiesController extends Controller
{
    protected $updateFacility = ['name'];

    public function getAllFacilities(){
        $facilites = Facility::all();
        $data = [
            'facilities' => $facilites
        ];
        return response()->json($data);

    }

    public function addFacility(Request $request){
        $validation =Validator::make($request->all(), [
            'name' => 'required|unique:facilities,name',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $facility = Facility::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'message' => 'Facility added successfully',
        ]);
    }

    public function updateFacility(Request $request,$id){
        $facility = Facility::find($id);
        $facility->update($request->only($this->updateFacility));
        return response()->json([
            'message' => 'Facility updated successfully',
        ]);
    }

    public function deleteFacility($id){
        $facility = Facility::find($id);
        $facility->delete();
        return response()->json([
            'message' => 'Facility deleted successfully',
        ]);
    }
}
