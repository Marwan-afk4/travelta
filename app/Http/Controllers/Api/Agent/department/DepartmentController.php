<?php

namespace App\Http\Controllers\Api\Agent\department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Department;

class DepartmentController extends Controller
{
    public function __construct(private Department $departments){}

    public function view(){
        // /department
        $departments = $this->departments
        ->get();

        return response()->json([
            'departments' => $departments
        ]);
    }
}
