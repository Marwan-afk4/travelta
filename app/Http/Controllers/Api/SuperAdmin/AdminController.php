<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\User;

class AdminController extends Controller
{
    public function __construct(private User $admin){}

    public function view(Request $request){
        $admin = $this->admin
        ->select('name', 'email', 'phone', 'id')
        ->where('role', 'SuperAdmin')
        ->get();

        return response()->json([
            'admin' => $admin,
        ]);
    }

    public function admin(Request $request, $id){
        $admin = $this->admin
        ->select('name', 'email', 'phone', 'id')
        ->where('role', 'SuperAdmin')
        ->where('id', $id)
        ->first();

        return response()->json([
            'admin' => $admin,
        ]);
    }

    public function create(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'password'=>'required',
            'email'=>'required|email|unique:users,id',
            'phone'=>'required|unique:users,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $adminRequest = $validation->validated();
        $admin = $this->admin
        ->create($adminRequest);

        return response()->json([
            'success' => 'You add admin success',
        ]);
    }

    public function modify(Request $request, $id){
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'password'=>'required',
            'email'=> ['required', 'email', Rule::unique('users')->ignore($id)],
            'phone'=> ['required', Rule::unique('users')->ignore($id)],
            ''=>'required|unique:users,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $adminRequest = $validation->validated();
        $adminRequest['password'] = Hash::make($request->password);
        $this->admin
        ->where('id', $id)
        ->update($adminRequest);

        return response()->json([
            'success' => 'You update admin success'
        ]);
	// name, email, password, phone
    }

    public function delete(Request $request, $id){
        $this->admin
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
