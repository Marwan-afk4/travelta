<?php

namespace App\Http\Controllers\Api\Agent\admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\admin\AdminRequest;
use Illuminate\Validation\Rule;

use App\Models\AdminAgentPosition;
use App\Models\AdminAgent;

class AdminController extends Controller
{
    public function __construct(private AdminAgentPosition $position, 
    private AdminAgent $admins){}

    public function view(Request $request){
        // /agent/admin
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
        else {
            $role = 'agent_id';
        }

        $admins = $this->admins
        ->where($role, $agent_id)
        ->with('position:id,name')
        ->get();
        $positions = $this->position
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'admins' => $admins,
            'positions' => $positions,
        ]);
    }

    public function admin(Request $request, $id){
        // /agent/admin/item/{id} 
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
        else {
            $role = 'agent_id';
        }

        $admin = $this->admins
        ->where($role, $agent_id)
        ->where('id', $id)
        ->with('position:id,name')
        ->first();

        return response()->json([
            'admin' => $admin, 
        ]);
    }

    public function status(Request $request, $id){ 
        // /agent/admin/status/{id}
        // Key
        // status
        $validation = Validator::make($request->all(), [
            'status' => 'required|boolean',
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
        else {
            $role = 'agent_id';
        }
        $admins = $this->admins
        ->where($role, $agent_id)
        ->where('id', $id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned'
        ]);
    }

    public function create(AdminRequest $request){ 
        // /agent/admin/add
        // Keys
        // position_id, name, email, phone, password, status 
        $validation = Validator::make($request->all(), [
            'email' => 'unique:admin_agents,email', 
            'phone' => 'unique:admin_agents,phone', 
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
        else {
            $role = 'agent_id';
        }

        $adminRequest = $request->validated();
        $adminRequest[$role] = $agent_id;
        $admins = $this->admins
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(AdminRequest $request, $id){ 
        // /agent/admin/update/{id}
        // Keys
        // position_id, name, email, phone, password, status 
        $validation = Validator::make($request->all(), [
            'email' => [Rule::unique('admin_agents')->ignore($id)],
            'phone' => [Rule::unique('admin_agents', 'phone')->ignore($id)],
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
        else {
            $role = 'agent_id';
        }

        $adminRequest = $request->validated();
        $adminRequest['password'] = bcrypt($adminRequest['password']);
        $admins = $this->admins
        ->where($role, $agent_id)
        ->where('id', $id)
        ->update($adminRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){ 
        // /agent/admin/delete/{id}
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
        else {
            $role = 'agent_id';
        }
        $admins = $this->admins
        ->where($role, $agent_id)
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
