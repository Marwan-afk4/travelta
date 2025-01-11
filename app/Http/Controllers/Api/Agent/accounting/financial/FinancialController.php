<?php

namespace App\Http\Controllers\Api\Agent\accounting\financial;

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
        // agent/financial/add
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
