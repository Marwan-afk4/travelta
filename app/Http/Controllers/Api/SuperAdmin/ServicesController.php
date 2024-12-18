<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicesController extends Controller
{


    public function services(){
        $services = Service::all();
        $data=[
            'services'=>$services
        ];
        return response()->json($data);
    }

    public function deleteService($id){
        $service=Service::find($id);
        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    public function addService(Request $request){
        $validation = Validator::make($request->all(), [
            'service_name' => 'required',
            'description' => 'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $service = Service::create([
            'service_name' => $request->service_name,
            'description' => $request->description,
        ]);
        return response()->json([
            'message' => 'Service added successfully',
        ]);
    }

    
}
