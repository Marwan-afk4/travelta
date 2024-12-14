<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    protected $updatedepartment = ['name', 'phone', 'email', 'whatsapp_number'];

    public function departments(){
        $departments = Department::all();
        $data=[
            'departments'=>$departments
        ];
        return response()->json($data);
    }

    public function addDepartment(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required|unique:departments,name',
            'phone' => 'required|unique:departments,phone',
            'email' => 'required|unique:departments,email',
            'whatsapp_number' => 'required|unique:departments,whatsapp_number',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $department = Department::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
        ]);
        return response()->json([
            'message' => 'Department added successfully',
            'department' => $department,
        ]);
    }

    public function deleteDepartment($id){
        $department=Department::find($id);
        $department->delete();
        return response()->json([
            'message' => 'Department deleted successfully',
        ]);
    }

    public function updateDepartment(Request $request,$id){
        $department=Department::find($id);
        $department->update($request->only($this->updatedepartment));
        return response()->json([
            'message' => 'Department updated successfully',
            'department' => $department,
        ]);
    }
}
