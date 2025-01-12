<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThemeController extends Controller
{

    protected $updateTheme = ['name'];


    public function getAllTheme(){
        $themes = Theme::all();
        $data = [
            'themes' => $themes
        ];
        return response()->json($data);
    }

    public function addTheme(Request $request){
        $validation =Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $theme = Theme::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'message' => 'Theme added successfully',
        ]);
    }

    public function updateTheme(Request $request,$id){
        $theme = Theme::find($id);
        $theme->update($request->only($this->updateTheme));
        return response()->json([
            'message' => 'Theme updated successfully',
        ]);
    }

    public function deleteTheme($id){
        $theme = Theme::find($id);
        $theme->delete();
        return response()->json([
            'message' => 'Theme deleted successfully',
        ]);
    }
}
