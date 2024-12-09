<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LegalPaper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function users(){
        $user = User::with('bookings')
        ->with('legalpaper')
        ->where('role','!=',['SuperAdmin','admin'])
        ->get();
        return response()->json([
            'users' => $user,
        ], 200);
    }

    public function adduser(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'phone' => 'required|unique:users,phone',
            'emergency_phone' => 'required|unique:users,emergency_phone',
            'image' => 'required|array',
            'image.*' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
        ]);

        foreach($request->image as $image){
            LegalPaper::create([
                'user_id' => $user->id,
                'image' => $image['image']
            ]);
        }
        return response()->json([
            'message' => 'User added successfully',
            'user' => $user
        ]);
    }

    public function deleteuser($id){
        $user=User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }


}
