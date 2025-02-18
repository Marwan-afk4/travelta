<?php

namespace App\Http\Controllers\Api\Agent\accounting\expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ExpensesCategory;

class ExpensesCategoryController extends Controller
{
    public function __construct(private ExpensesCategory $categories){}

    public function view(Request $request){
        // agent/accounting/expenses/category
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $categories = $this->categories
        ->select('id', 'name', 'category_id')
        ->where($agent_type, $agent_id)
        ->with(['parent_category:id,name'])
        ->get();
        $parent_categories = $this->categories
        ->select('id', 'name', 'category_id')
        ->where($agent_type, $agent_id)
        ->whereNull('category_id')
        ->get();

        return response()->json([
            'categories' => $categories,
            'parent_categories' => $parent_categories,
        ]);
    } 

    public function category(Request $request, $id){
        // agent/accounting/expenses/category/item/{id}
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $category = $this->categories
        ->select('id', 'name', 'category_id')
        ->with(['parent_category:id,name'])
        ->where($agent_type, $agent_id)
        ->where('id', $id)
        ->first();

        return response()->json([
            'category' => $category,
        ]);
    } 

    public function create(Request $request){
        // agent/accounting/expenses/category/add
        // Keys
        // name, category_id
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'nullable|exists:expenses_categories,id'
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $categoryRequest = $validation->validated();
        $categoryRequest[$agent_type] = $agent_id;
        $this->categories
        ->create($categoryRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request, $id){
        // agent/accounting/expenses/category/update/{id}
        // Keys
        // name, category_id
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'nullable|exists:expenses_categories,id'
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $categoryRequest = $validation->validated();
        $categoryRequest[$agent_type] = $agent_id;
        $this->categories
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->update($categoryRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // agent/accounting/expenses/category/delete/{id}
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }
  
        $this->categories
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
