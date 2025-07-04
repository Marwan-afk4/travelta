<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Authcontroller extends Controller
{

    public function register(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable',
            'emergency_phone' => 'required',
            'legal_paper' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
            'legal_paper' => $request->legal_paper,
            'type' => 'user',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 200);
    } 

    public function login(Request $request){
        $Validation = Validator::make(request()->all(), [
            'email' => 'nullable|email|exists:users,email',
            'phone' => 'nullable|exists:users,phone',
            'password' => 'required|min:6'
        ]);
        if($Validation->fails()){
            return response()->json(['errors' => $Validation->errors()], 401);
        }
        $user=User::where('email',$request->email)
        ->orWhere('phone',$request->phone)
        ->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User successfully logged in',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request){
        $user =auth()->user();
        $deletToken = $user->tokens()->delete();
        if ($deletToken) {
            return response()->json([
                'success' => 'You logout success'
            ]);
        } else {
            return response()->json([
                'faild' => 'You faild to logout'
            ], 400);
        }
    }

    public function my_profile(Request $request){
        $user = User::
        select('name', 'email', 'phone')
        ->where('id', $request->user()->id)
        ->first();

        return response()->json([
            'user' => $user
        ]);
    }

    public function update_my_profile(Request $request){
    
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($request->user()->id)],
            'phone' => ['required', Rule::unique('users')->ignore($request->user()->id)],
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $data = [
            'name' => $request->name, 
            'email' => $request->email,
            'phone' => $request->phone,
        ];
        if(!empty($request->password)){
            $data['password'] = Hash::make($request->password);
        }
        User::where('id', $request->user()->id)
        ->update($data);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }
}
