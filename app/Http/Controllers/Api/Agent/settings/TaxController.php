<?php

namespace App\Http\Controllers\Api\Agent\settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\settings\tax\TaxRequest;
use Illuminate\Http\Request;

use App\Models\Tax;
use App\Models\Country;

class TaxController extends Controller
{
    public function __construct(private Tax $tax, private Country $countries){}

    public function view(Request $request){
        // /settings/tax
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
            $tax = $this->tax
            ->where('affilate_id', $agent_id)
            ->with('country')
            ->get();
        } 
        else {
            $tax = $this->tax
            ->with('country')
            ->where('agent_id', $agent_id)
            ->get();
        } 
        $countries = $this->countries
        ->get();

        return response()->json([
            'tax' => $tax,
            'countries' => $countries,
        ]);
    }

    public function tax($id){
        // /settings/tax/item/{id}
        $tax = $this->tax
        ->where('id', $id)
        ->with('country')
        ->first(); 

        return response()->json([
            'tax' => $tax, 
        ]);
    }

    public function create(TaxRequest $request){
        // /settings/tax/add
        // Keys
        // name, country_id, type, amount
        $taxRequest = $request->validated();
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
            $taxRequest['affilate_id']  = $agent_id; 
            $tax = $this->tax
            ->create($taxRequest);
        } 
        else {
            $taxRequest['agent_id']  = $agent_id; 
            $tax = $this->tax
            ->create($taxRequest); 
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(TaxRequest $request, $id){
        // /settings/tax/update/{id}
        // Keys
        // name, country_id, type, amount
        $taxRequest = $request->validated(); 
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
            $tax = $this->tax
            ->where('id', $id)
            ->where('affilate_id', $agent_id)
            ->update($taxRequest);
        } 
        else { 
            $tax = $this->tax
            ->where('id', $id)
            ->where('agent_id', $agent_id)
            ->update($taxRequest); 
        }

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /settings/tax/delete/{id}
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
            $tax = $this->tax
            ->where('id', $id)
            ->where('affilate_id', $agent_id)
            ->delete();
        } 
        else { 
            $tax = $this->tax
            ->where('id', $id)
            ->where('agent_id', $agent_id)
            ->delete(); 
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
