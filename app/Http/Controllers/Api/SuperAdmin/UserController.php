<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\LegalPaper;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

class UserController extends Controller
{
    use image;
    public function users(){
        $user = Customer::with('bookings')
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
        $user = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
        ]);
        foreach($request->image as $image){
            $imag_path = $this->uploadFile($image['image'], 'admin/legal_paper/user');
            LegalPaper::create([
                'user_id' => $user->id,
                'image' => $imag_path
            ]);
        }
        return response()->json([
            'message' => 'User added successfully',
            'user' => $user
        ]);
    }

    public function deleteuser($id){
        $user=User::find($id);
        $legal_papers = LegalPaper
        ->where('user_id', $user->id)
        ->get();
        foreach ($legal_papers as $item) {
            $this->deleteImage($item->image);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }


}
