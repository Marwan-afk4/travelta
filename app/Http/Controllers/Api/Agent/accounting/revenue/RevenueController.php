<?php

namespace App\Http\Controllers\Api\Agent\accounting\revenue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\accounting\revenue\RevenueRequest;

use App\Models\RevenueCategory;
use App\Models\Revenue;
use App\Models\FinantiolAcounting;
use App\Models\CurrencyAgent;

class RevenueController extends Controller
{
    public function __construct(private RevenueCategory $categories,
    private Revenue $revenue, private FinantiolAcounting $finantiol,
    private CurrencyAgent $currency){}

    public function view(Request $request){
        // agent/accounting/revenue
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

        $revenue = $this->revenue
        ->where($agent_type, $agent_id)
        ->with(['category:id,name', 'financial:id,name,logo', 'currency:id,name'])
        ->get();

        return response()->json([
            'revenue' => $revenue
        ]);
    }
    
    public function lists(Request $request){
        // agent/accounting/revenue/lists
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
        // agent/accounting/revenue/filter
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

        $revenue = $this->revenue
        ->where($agent_type, $agent_id)
        ->with(['category:id,name', 'financial:id,name,logo', 'currency:id,name'])
        ->get();
        if ($request->from) {
            $revenue = $revenue->where('date', '>=', $request->from);
        }
        if ($request->to) {
            $revenue = $revenue->where('date', '<=', $request->to);
        }

        return response()->json([
            'revenue' => $revenue->values()
        ]);
    }

    public function create(RevenueRequest $request){
        // agent/accounting/revenue/add
        // Keys
        // category_id, financial_id, currency_id
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

        $RevenueRequest = $request->validated();
        $RevenueRequest[$agent_type] = $agent_id;
        $this->revenue
        ->create($RevenueRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(RevenueRequest $request, $id){
        // agent/accounting/revenue/update/{id}
        // Keys
        // category_id, financial_id, currency_id
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

        $RevenueRequest = $request->validated(); 
        $this->revenue
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->update($RevenueRequest);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // agent/accounting/revenue/delete/{id}
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
        
        $this->revenue
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
