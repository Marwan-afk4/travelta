<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacilitiesController extends Controller
{
    use image;
    protected $updateFacility = ['name','logo'];

    public function getAllFacilities(){
        $facilites = Facility::all();
        $data = $facilites->map(function ($facility) {
            return [
                'id' => $facility->id,
                'name' => $facility->name,
                'icon'=>$facility->logo ? url('storage/' . $facility->logo) : null,
            ];
        });
        return response()->json($data);

    }

    public function addFacility(Request $request){
        $validation =Validator::make($request->all(), [
            'name' => 'required|unique:facilities,name',
            'logo' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $facility = Facility::create([
            'name' => $request->name,
            'logo' => $this->storeBase64Image($request->logo, 'admin/facility/logos'),
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
