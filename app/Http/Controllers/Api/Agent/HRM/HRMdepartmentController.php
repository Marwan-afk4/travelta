<?php

namespace App\Http\Controllers\Api\Agent\HRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\HrmDepartment;

class HRMdepartmentController extends Controller
{
    public function __construct(private HrmDepartment $department){}

    public function view(Request $request){
        // /agent/hrm/department
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

        $departments = $this->department
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'departments' => $departments
        ]);
    }

    public function department(Request $request, $id){
        // /agent/hrm/department/item/{id}
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

        $department = $this->department
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();

        return response()->json([
            'department' => $department
        ]);
    }

    public function status(Request $request){
        // /agent/hrm/department/status/{id}
        // keys
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
        else{
            $role = 'agent_id';
        }

        $departments = $this->department
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active': 'banned'
        ]);
    }

    public function create(Request $request){
        // /agent/hrm/department/add
        // keys
        // name, status
        $validation = Validator::make($request->all(), [
            'name' => 'required',
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
        else{
            $role = 'agent_id';
        }

        $departmentRequest = $validation->validated();
        $departmentRequest[$role] = $agent_id;
        $departments = $this->department
        ->create($departmentRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // /agent/hrm/department/update/{id}
        // keys
        // name, status
        $validation = Validator::make($request->all(), [
            'name' => 'required',
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
        else{
            $role = 'agent_id';
        }

        $departmentRequest = $validation->validated();
        $departments = $this->department
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update($departmentRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/hrm/department/delete/{id}
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

        $departments = $this->department
        ->where('id', $id)
        ->where($role, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
