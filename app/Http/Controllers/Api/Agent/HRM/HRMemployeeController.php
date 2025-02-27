<?php

namespace App\Http\Controllers\Api\Agent\HRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use App\Http\Requests\api\agent\hrm\HRMRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\HrmEmployee;
use App\Models\HrmDepartment;
use App\Models\AdminAgentPosition;

class HRMemployeeController extends Controller
{
    public function __construct(private HrmEmployee $employee,
    private HrmDepartment $departments, private AdminAgentPosition $role){}
    use image;

    public function view(Request $request){
        // /agent/hrm/employee
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

        $employees = $this->employee
        ->with('department:id,name')
        ->where($role, $agent_id)
        ->where('status', 1)
        ->get();
        $departments = $this->departments
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->where('status', 1)
        ->get();

        return response()->json([
            'employees' => $employees,
            'departments' => $departments
        ]);
    }

    public function employee(Request $request, $id){
        // /agent/hrm/employee/item/{id}
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

        $employee = $this->employee
        ->with('department:id,name')
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first(); 

        return response()->json([
            'employee' => $employee, 
        ]);
    }

    public function create(HRMRequest $request){
        // /agent/hrm/employee/add
        // keys
        // name, department_id, address, phone, email, image
        $validation = Validator::make($request->all(), [
            'email' => 'unique:hrm_employees,email',
            'phone' => 'unique:hrm_employees,phone',
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

        $employeeRequest = $request->validated();
        $employeeRequest[$role] = $agent_id;
        $role = $this->role
        ->where('name', 'agent')
        ->where($role, $agent_id)
        ->first();
        if (empty($role)) {
            $role = $this->role
            ->create([
                'name' => 'agent',
                $role => $agent_id,
            ]);
        }
        $employeeRequest['role_id'] = $role->id;
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'agent/hrm/employee');
            $employeeRequest['image'] = $image_path;
        }
        $employees = $this->employee
        ->create($employeeRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(HRMRequest $request, $id){
        // /agent/hrm/employee/update/{id}
        // keys
        // name, department_id, address, phone, email, image
        $validation = Validator::make($request->all(), [
            'email' => [Rule::unique('hrm_employees')->ignore($id)],
            'phone' => [Rule::unique('hrm_employees')->ignore($id)],
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

        $employee = $this->employee
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();
        $employeeRequest = $request->validated();
        if ($request->image && !is_string($request->image)) {
            $image_path = $this->upload($request, 'image', 'agent/hrm/employee');
            $this->deleteImage($employee->image);
            $employeeRequest['image'] = $image_path;
        }
        $employee->update($employeeRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/hrm/employee/delete/{id}
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

        $employee = $this->employee
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first(); 
        $employee->update([
            'status' => 0
        ]); 

        return response()->json([
            'success' => 'You delete data success',
        ]);
    }
}
