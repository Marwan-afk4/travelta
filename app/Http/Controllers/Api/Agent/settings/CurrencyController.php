<?php

namespace App\Http\Controllers\Api\Agent\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\settings\currency\CurrencyRequest;

use App\Models\Currancy;
use App\Models\CurrencyAgent;

class CurrencyController extends Controller
{
    public function __construct(private Currancy $currency, 
    private CurrencyAgent $currency_agent){}

    public function view(Request $request){
        // /settings/currency
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
            $currency_agent = $this->currency_agent
            ->where('affilate_id', $agent_id)
            ->get();
        } 
        else {
            $currency_agent = $this->currency_agent
            ->where('agent_id', $agent_id)
            ->get();
        } 
        $currency = $this->currency
        ->select('id', 'currancy_symbol', 'currancy_name')
        ->get();

        return response()->json([
            'currency_agent' => $currency_agent,
            'currencies' => $currency,
        ]);
    }

    public function currency(Request $request, $id){
        // /settings/currency/item/{id}
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
            $currency_agent = $this->currency_agent
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->first();
        } 
        else {
            $currency_agent = $this->currency_agent
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->first();
        }

        return response()->json([
            'currency_agent' => $currency_agent,
        ]);
    }

    public function create(CurrencyRequest $request){
        // /settings/currency/add
        // Keys
        // currancy_id, name, point
        $currencyRequest = $request->validated();
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
            $currencyRequest['affilate_id'] = $agent_id;
            $currency_agent = $this->currency_agent
            ->create($currencyRequest);
        } 
        else {
            $currencyRequest['agent_id'] = $agent_id;
            $currency_agent = $this->currency_agent
            ->create($currencyRequest);
        }

        return response()->json([
            'success' => $currency_agent
        ]);
    }

    public function modify(CurrencyRequest $request, $id){
        // /settings/currency/update/{id}
        // Keys
        // currancy_id, name, point
        $currencyRequest = $request->validated();
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
            $currency_agent = $this->currency_agent
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->first();
            $currency_agent->update($currencyRequest);
        } 
        else {
            $currency_agent = $this->currency_agent
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->first();
            $currency_agent->update($currencyRequest);
        }

        return response()->json([
            'success' => $currency_agent
        ]);
    }

    public function delete(Request $request, $id){
        // /settings/currency/delete/{id}
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
            $currency_agent = $this->currency_agent
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->delete(); 
        } 
        else {
            $currency_agent = $this->currency_agent
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->delete(); 
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
