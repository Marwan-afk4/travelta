<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TourType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TourTypeController extends Controller
{
    protected $updateTourType =['name'];

    public function getTourtype(){
        $tourtype = TourType::all();
        $data = [
            'tourtype' => $tourtype
        ];
        return response()->json($data);
    }

    public function addTourtype(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => ['required','unique:tour_types,name']
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $tourtype = TourType::create([
            'name' => $request->name
        ]);
        return response()->json([
            'message' => 'Tour type added successfully',
        ]);
    }

    public function deleteTourtype(Request $request){
        $tourtype = TourType::find($request->id);
        $tourtype->delete();
        return response()->json([
            'message' => 'Tour type deleted successfully',
        ]);
    }

    public function updateTourtype(Request $request,$id){
        $tourtype = TourType::find($id);
        $tourtype->update($request->only($this->updateTourType));
        return response()->json(['message'=>'type updated successfully']);
    }
}
