<?php

namespace App\Http\Controllers\Api\Agent\accounting_methods\financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\accounting\FinancialRequest;
use Illuminate\Support\Facades\Validator;
use App\trait\image;
 
use App\Models\FinantiolAcounting;
use App\Models\CurrencyAgent;

class FinancialController extends Controller
{
    use image;
    public function __construct(private FinantiolAcounting $financial, private CurrencyAgent $currencies){}

    public function view(Request $request){
        // /agent/financial
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
        $financial = $this->financial 
        ->where($role, $agent_id)
        ->with('currancy')
        ->get();
        $currencies = $this->currencies
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'financials' => $financial,
            'currencies' => $currencies,
        ]);
    }

    public function status(Request $request, $id){
        // agent/financial/status/{id}
        // Keys
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
        $financial = $this->financial
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]); 

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }

    public function transfer(Request $request){
        $validation = Validator::make($request->all(), [
            'from_financial_id' => 'required|exists:finantiol_acountings,id',
            'to_financial_id' => 'required|exists:finantiol_acountings,id',
            'amount' => 'required|numeric',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
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
        $financial_from = $this->financial
        ->where('id', $request->from_financial_id)
        ->where($role, $agent_id)
        ->first();
        $financial_to = $this->financial
        ->where('id', $request->to_financial_id)
        ->where($role, $agent_id)
        ->first();
        if (empty($financial_from)) {
            return response()->json([
                'errors' => 'Financial from is not found'
            ], 400);
        }
        if (empty($financial_to)) {
            return response()->json([
                'errors' => 'Financial to is not found'
            ], 400);
        }
        if ($financial_from->currency_id != $financial_to->currency_id) {
            return response()->json([
                'errors' => 'Currency is not supported'
            ], 400);
        }
        if ($financial_from->balance < $request->amount) {
            return response()->json([
                'errors' => 'Amount is bigger than balance of financial'
            ], 400);
        }

        $financial_from->balance -= $request->amount;
        $financial_to->balance += $request->amount;
        $financial_from->save();
        $financial_to->save();
        
        return response()->json([
            'success' => 'You transfer money success'
        ]);
    }
    
    public function financial(Request $request, $id){
        // /agent/financial/item/{id}
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
        $financial = $this->financial 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->with('currancy')
        ->first();

        return response()->json([
            'financial' => $financial,
        ]);
    }

    public function create(FinancialRequest $request){
        // agent/financial/add
        // Keys
        // name, details, balance, currency_id, status, logo
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
        $financialRequest = $request->validated();
        $financialRequest[$role] = $agent_id;
        if (!is_string($request->logo)) {
            $image_path = $this->upload($request, 'logo', 'agent/financial/logo');
            $financialRequest['logo'] = $image_path;
        }
        $financial = $this->financial
        ->create($financialRequest);

        return response()->json([
            'success' => $financial,
        ]);
    }

    public function modify(FinancialRequest $request, $id){
        // agent/financial/update/{id}
        // Keys
        // name, details, balance, currency_id, status, logo
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
        $financialRequest = $request->validated();
        $financial = $this->financial
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        if (empty($financial)) {
            return response()->json([
                'errors' => 'financial is not found'
            ], 400);
        }
        if (!is_string($request->logo)) {
            $image_path = $this->update_image($request, $financial->logo, 'logo', 'agent/financial/logo');
            $financialRequest['logo'] = $image_path;
        }
        $financial->update($financialRequest);

        return response()->json([
            'success' => $financial,
        ]);
    }

    public function delete(Request $request, $id){
        // agent/financial/delete/{id}
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
        $financial = $this->financial
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        if (empty($financial)) {
            return response()->json([
                'errors' => 'financial is not found'
            ], 400);
        }
        $this->deleteImage($financial->logo);
        $financial->delete();

        return response()->json([
            'success' => 'You delete financial success'
        ], 200);
    }

}
