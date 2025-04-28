<?php

namespace App\Http\Controllers\Api\Agent\accounting\expenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\accounting\expenses\ExpensesRequest;

use App\Models\ExpensesCategory;
use App\Models\Expense;
use App\Models\FinantiolAcounting;
use App\Models\CurrencyAgent;

class ExpensesController extends Controller
{
    public function __construct(private ExpensesCategory $categories,
    private Expense $expenses, private FinantiolAcounting $finantiol,
    private CurrencyAgent $currency){}

    public function view(Request $request){
        // agent/accounting/expenses
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

        $expenses = $this->expenses
        ->where($agent_type, $agent_id)
        ->with(['category:id,name', 'financial:id,name,logo', 'currency:id,name'])
        ->get();

        return response()->json([
            'expenses' => $expenses
        ]);
    }
    
    public function lists(Request $request){
        // agent/accounting/expenses/lists
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
        $finantiols = $this->finantiol
        ->where($agent_type, $agent_id)
        ->get();
        $currencies = $this->currency
        ->where($agent_type, $agent_id)
        ->get();
        $categories = $this->categories
        ->where($agent_type, $agent_id)
        ->get();

        return response()->json([
            'finantiols' => $finantiols,
            'currencies' => $currencies,
            'categories' => $categories,
        ]);
    }

    public function filter(Request $request){
        // agent/accounting/expenses/filter
        // Keys
        // from, to
        $validation = Validator::make($request->all(), [
            'from' => 'nullable|date',
            'to' => 'nullable|date'
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

        $expenses = $this->expenses
        ->where($agent_type, $agent_id)
        ->with(['category:id,name', 'financial:id,name,logo', 'currency:id,name'])
        ->get();
        if ($request->from) {
            $expenses = $expenses->where('date', '>=', $request->from);
        }
        if ($request->to) {
            $expenses = $expenses->where('date', '<=', $request->to);
        }

        return response()->json([
            'expenses' => $expenses->values()
        ]);
    }

    public function create(ExpensesRequest $request){
        // agent/accounting/expenses/add
        // Keys
        // category_id, financiale_id , currency_id
        // title, date, amount, description
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

        $expensesRequest = $request->validated();
        $expensesRequest[$agent_type] = $agent_id;
        $this->expenses
        ->create($expensesRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(ExpensesRequest $request, $id){
        // agent/accounting/expenses/update/{id}
        // Keys
        // category_id, financiale_id , currency_id
        // title, date, amount, description
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

        $expensesRequest = $request->validated(); 
        $this->expenses
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->update($expensesRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // agent/accounting/expenses/delete/{id}
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
        
        $this->expenses
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
