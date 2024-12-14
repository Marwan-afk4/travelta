<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Currancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrancyController extends Controller
{


    public function currancy(){
        $currancy = Currancy::all();
        $data = [
            'currancy' => $currancy
        ];
        return response()->json($data);
    }

    public function addCurrancy(Request $request){
        $validation = Validator::make($request->all(), [
            'currancy_name' => 'required',
            'currancy_symbol' => 'required',
            'currancy_code' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $currancy = Currancy::create([
            'currancy_name' => $request->currancy_name,
            'currancy_symbol' => $request->currancy_symbol,
            'currancy_code' => $request->currancy_code
        ]);
        return response()->json([
            'message' => 'Currancy added successfully',
        ]);
    }

    public function deleteCurrancy($id){
        $currancy=Currancy::find($id);
        $currancy->delete();
        return response()->json([
            'message' => 'Currancy deleted successfully',
        ]);
    }
}
