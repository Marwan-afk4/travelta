<?php

namespace App\Http\Controllers\Api\Agent\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\AdminAgent;
use App\Models\AffilateAgent;
use App\Models\Agent;

class ProfileController extends Controller
{ 
    
    public function __construct(private AffilateAgent $affilate_agent, private Agent $agent,
    private AdminAgent $admin_agent){}

    public function my_profile(Request $request){
        $name = null;
        $phone = null;
        $email = null;
        $role = null;
        if (!empty($request->user()->admin_agents)) {
            $user = $this->admin_agent 
            ->where('id', $request->user()->id)
            ->first();
            $name = $user->name;
            $phone = $user->phone;
            $email = $user->email;
            $role = empty($user->affilate_id) ? 'agent_admin' : 'affilate_admin';
        }
        else{
            if($request->user()->role == 'affilate' || $request->user()->role == 'freelancer'){
                $user = $this->affilate_agent 
                ->where('id', $request->user()->id)
                ->first();
                $name = $user->f_name . ' ' . $user->l_name;
                $phone = $user->phone;
                $email = $user->email;
                $role = 'affilate';
            }
            else{
                $user = $this->agent 
                ->where('id', $request->user()->id)
                ->first();
                $name = $user->name;
                $phone = $user->phone;
                $email = $user->email;
                $role = 'agent';
            }
        }

        return response()->json([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'role' => $role,
        ]);
    }

    public function update_profile(Request $request){
        // role => [affilate, agent]
        // if affilate => f_name, l_name, email, phone, password
        // if agent => name, email, phone, password

        $role = null;
        if(!empty($request->user()->admin_agents)){
            $role = empty($request->user()->affilate_id) ? 'agent_admin' : 'affilate_admin';
        }
        else{
            if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
                $role = 'affilate';
            }
            else{
                $role = 'agent';
            }
        }
 
        if ($role == 'affilate') {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => [Rule::unique('affilate_agents')->ignore($request->user()->id)],
                'phone' => [Rule::unique('affilate_agents')->ignore($request->user()->id)],
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            } 
            $data = [
                'f_name' => $request->name,
                'l_name' => ' ',
                'email' => $request->email,
                'phone' => $request->phone,
            ];
            if(!empty($request->password)){
                $data['password'] = Hash::make($request->password);
            }
            $this->affilate_agent
            ->where('id', $request->user()->id)
            ->update($data);
        }
        elseif($role == 'agent'){ 
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => [Rule::unique('agents')->ignore($request->user()->id)],
                'phone' => [Rule::unique('agents')->ignore($request->user()->id)],
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
            ->where('id', $request->user()->id)
            ->update($data);
        }
        else{ 
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => [Rule::unique('admin_agents')->ignore($request->user()->id)],
                'phone' => [Rule::unique('admin_agents')->ignore($request->user()->id)],
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
            $this->admin_agent
            ->where('id', $request->user()->id)
            ->update($data);
        }

        return response()->json([
            'success' => 'You update profile success'
        ]);
    }
}
