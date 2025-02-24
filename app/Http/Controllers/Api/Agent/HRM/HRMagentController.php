<?php

namespace App\Http\Controllers\Api\Agent\HRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HrmEmployee;

class HRMagentController extends Controller
{
    public function __construct(private HrmEmployee $agents){}

    public function view(Request $request){
        // /agent/hrm/agent
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }

        $agents = $this->agents
        ->where($role, $agent_id)
        ->where('agent', 1)
        ->where('status', 1)
        ->get();

        return response()->json([
            'agents' => $agents
        ]);
    }

    public function search(Request $request){
        // /agent/hrm/agent/search
        $validation = Validator::make($request->all(), [
            'searc' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
    }

    public function add(Request $request, $id){
        // /agent/hrm/agent/add
        // Keys
        // user_name, password
        $validation = Validator::make($request->all(), [
            'user_name' => 'required',
            'password' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
 
        $this->agents
        ->where($role, $agent_id)
        ->where('status', 1)
        ->where('id', $id)
        ->update([
            'user_name' => $request->user_name,
            'password' => $request->password,
            'agent' => 1,
        ]);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function delete(Request $request, $id){
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
 
        $this->agents
        ->where($role, $agent_id)
        ->where('', 1)
        ->where('id', $id)
        ->update([ 
            'status' => 0,
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
