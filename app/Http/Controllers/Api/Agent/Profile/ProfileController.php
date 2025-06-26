<?php

namespace App\Http\Controllers\Api\Agent\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\AffilateAgent;
use App\Models\Agent;

class ProfileController extends Controller
{ 
    
    public function __construct(private AffilateAgent $affilate_agent, private Agent $agent){}

    public function my_profile_agent(Request $request){
        $user = $this->affilate_agent
        ->select('name', 'phone', 'email')
            ->where('id', $request->user()->id)
            ->update($data);

        return response()->json([
            'data' => $user,
        ]);
    }

    public function my_profile_affilate(Request $request){
        $user = $this->affilate_agent
        ->select('f_name', 'l_name', 'phone', 'email')
        ->where('id', $request->user()->id)
        ->first();

        return response()->json([
            'data' => $user,
        ]);
    }

    public function update_profile(Request $request, $id){
        // role => [affilate, agent]
        // if affilate => f_name, l_name, email, phone, password
        // if agent => name, email, phone, password
        $validation = Validator::make($request->all(), [
            'role' => 'required|in:affilate,agent',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
 
        if ($request->role == 'affilate') {
            $validation = Validator::make($request->all(), [
                'f_name' => 'required',
                'l_name' => 'required',
                'email' => [Rule::unique('affilate_agents')->ignore($id)],
                'phone' => [Rule::unique('affilate_agents')->ignore($id)],
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            }
            $data = [
                'f_name' => $request->f_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];
            if(!empty($request->password)){
                $data['password'] = Hash::make($request->password);
            }
            $this->affilate_agent
            ->where('id', $id)
            ->update($data);
        }
        else{ 
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => [Rule::unique('agents')->ignore($id)],
                'phone' => [Rule::unique('agents')->ignore($id)],
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
            $this->agent
            ->where('id', $id)
            ->update($data);
        }
    }
}
