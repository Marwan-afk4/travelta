<?php

namespace App\Http\Controllers\Api\Agent\HRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 

use App\Models\HrmEmployee;
use App\Models\HrmDepartment;
use App\Models\Agent;
use App\Models\AffilateAgent;

class HRMagentController extends Controller
{
    public function __construct(private HrmEmployee $agents, 
    private HrmDepartment $department, private Agent $admin_agents,
    private AffilateAgent $affilate){}

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
        if ($request->user()?->plan?->module_type != 'hrm') {
            return response()->json([
                'errors' => 'Your plan does not support hrm'
            ], 400);
        }

        $agents = $this->agents
        ->with('department:id,name')
        ->where($role, $agent_id)
        ->where('agent', 1)
        ->where('status', 1)
        ->get();
        $departments = $this->department
        ->where($role, $agent_id)
        ->get();
        $employees = $this->agents
        ->where($role, $agent_id)
        ->where('agent', 0)
        ->where('status', 1)
        ->get();

        return response()->json([
            'agents' => $agents,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    public function agent(Request $request, $id){
        // /agent/hrm/agent/item/{id}
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
        if ($request->user()?->plan?->module_type != 'hrm') {
            return response()->json([
                'errors' => 'Your plan does not support hrm'
            ], 400);
        }

        $agent = $this->agents
        ->select('id', 'user_name')
        ->where($role, $agent_id)
        ->where('agent', 1)
        ->where('status', 1)
        ->first();

        return response()->json([
            'agent' => $agent,
        ]);
    }

    public function add(Request $request){
        // /agent/hrm/agent/add
        // Keys
        // user_name, password, employee_id
        $validation = Validator::make($request->all(), [
            'user_name' => 'required|unique:hrm_employees,user_name',
            'password' => 'required',
            'employee_id' => 'required',
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
            $agent = $this->affilate
            ->where('id', $agent_id)
            ->first();
        } 
        else {
            $role = 'agent_id';
            $agent = $this->admin_agents
            ->where('id', $agent_id)
            ->first();
        }
        if ($request->user()?->plan?->module_type != 'hrm') {
            return response()->json([
                'errors' => 'Your plan does not support hrm'
            ], 400);
        }

        if ($agent->users >= $agent->plan->user_limit) {
            return response()->json([
                'errors' => 'it has exceeded the maximum number of admins'
            ], 400);
        }
        $agent->users ++;
        $agent->save();
 
        $this->agents
        ->where($role, $agent_id)
        ->where('status', 1)
        ->where('id', $request->employee_id)
        ->update([
            'user_name' => $request->user_name,
            'password' => bcrypt($request->password),
            'agent' => 1,
        ]);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // /agent/hrm/agent/update/{id}
        // Keys
        // user_name, password
        $validation = Validator::make($request->all(), [ 
            'user_name' => [Rule::unique('hrm_employees')->ignore($id), 'required'],
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
        if ($request->user()?->plan?->module_type != 'hrm') {
            return response()->json([
                'errors' => 'Your plan does not support hrm'
            ], 400);
        }

        $agent_update = [
            'user_name' => $request->user_name,
        ];
        if ($request->password && !empty($request->password)) {
            $agent_update['password'] = bcrypt($request->password);
        }
        $this->agents
        ->where($role, $agent_id)
        ->where('status', 1)
        ->where('agent', 1)
        ->where('id', $id)
        ->update($agent_update);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/hrm/agent/delete/{id}
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
            $agent = $this->affilate
            ->where('id', $agent_id)
            ->first();
        } 
        else {
            $role = 'agent_id';
            $agent = $this->admin_agents
            ->where('id', $agent_id)
            ->first();
        }
        if ($request->user()?->plan?->module_type != 'hrm') {
            return response()->json([
                'errors' => 'Your plan does not support hrm'
            ], 400);
        }
        
        $agent->users --;
        $agent->save();
        $this->agents
        ->where($role, $agent_id) 
        ->where('id', $id)
        ->update([ 
            'status' => 0,
        ]);

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
