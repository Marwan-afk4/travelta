<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    protected $updatePlan=['name','description','user_limit','branch_limit','period_in_days','module_type','price','discount_type','discount_value','admin_cost','branch_cost','type'];

    public function plans(){
        $affilatePlans=Plan::where('type','affiliate')->get();
        $freelancerPlans=Plan::where('type','freelancer')->get();
        $agencyPlans=Plan::where('type','agency')->get();
        $suplierPlans=Plan::where('type','suplier')->get();
        return response()->json([
            'affilatePlans' => $affilatePlans,
            'freelancerPlans' => $freelancerPlans,
            'agencyPlans' => $agencyPlans,
            'suplierPlans' => $suplierPlans,
        ]);
    }

    public function addplan(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'user_limit' => 'required|integer',
            'branch_limit' => 'required|integer',
            'period_in_days' => 'required|integer',
            'module_type' => 'required|in:hrm,crm',
            'price' => 'required|numeric',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'admin_cost' => 'nullable|numeric',
            'branch_cost' => 'nullable|numeric',
            'type' => 'required|in:affiliate,freelancer,agency,suplier'
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $price_after_discount = $request->price_after_discount;
        if($request->discount_type == 'fixed') {
            $price_after_discount = $request->price - $request->discount_value;
        }elseif($request->discount_type == 'percentage') {
            $price_after_discount = $request->price - ($request->price * $request->discount_value / 100);
        }


        $plan = Plan::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_limit' => $request->user_limit,
            'branch_limit' => $request->branch_limit,
            'period_in_days' => $request->period_in_days,
            'module_type' => $request->module_type,
            'price' => $request->price,
            'discount_type'=>$request->discount_type,
            'discount_value'=>$request->discount_value,
            'price_after_discount' => $price_after_discount ?? $request->price,
            'admin_cost' => $request->admin_cost,
            'branch_cost' => $request->branch_cost,
            'type' => $request->type
        ]);
        return response()->json([
            'message' => 'Plan added successfully',
            'plan' => $plan
        ]);
    }

    public function deletePlan($id){
        $plan=Plan::find($id);
        $plan->delete();
        return response()->json([
            'message' => 'Plan deleted successfully',
        ]);
    }

    public function UpdatePlan(Request $request,$id){
        $plan = Plan::find($id);
        $plan->update($request->only($this->updatePlan));
        return response()->json([
            'message' => 'Plan updated successfully',
        ]);
    }



}
