<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\auth\agent\LoginRequest;

use App\Models\Agent;

class AgentAuthController extends Controller
{
    public function __construct(private Agent $agent){}

    public function signup_affilate(){
        
    }

    public function login(LoginRequest $request){
        // https://bcknd.food2go.online/api/user/auth/login
        // Keys
        // email, password
        $user = $this->agent
        ->where('email', $request->email)
        ->orWhere('phone', $request->email)
        ->first();
        
        if (password_verify($request->input('password'), $user->password)) {
            $user->token = $user->createToken($user->role)->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $user->token,
            ], 200);
        }
        else { 
            return response()->json(['faield'=>'creational not Valid'],403);
        }
    }
}
