<?php

namespace App\Http\Controllers\Api\Agent\accounting\OE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\accounting\OE\OwnerRequest;

use App\Models\Owner;
use App\Models\CurrencyAgent;
use App\Models\FinantiolAcounting;

class OwnerController extends Controller
{
    public function __construct(private Owner $owners, private CurrencyAgent $currencies,
    private FinantiolAcounting $financial){}

    public function view(Request $request){
        // /agent/accounting/owner
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

        $owners = $this->owners
        ->with(['currency:id,name'])
        ->where($role, $agent_id)
        ->get(); 

        return response()->json([
            'owners' => $owners, 
        ]);
    }

    public function lists(Request $request){
        // /agent/accounting/owner/lists
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
 
        $currencies = $this->currencies
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get();
        $financials = $this->financial
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get();

        return response()->json([ 
            'currencies' => $currencies,
            'financials' => $financials,
        ]);
    }

    public function owner(Request $request, $id){
        // /agent/accounting/owner/item/{id}
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

        $owner = $this->owners
        ->with(['currency:id,name'])
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();

        return response()->json([
            'owner' => $owner
        ]);
    }

    public function create(OwnerRequest $request){
        // /agent/accounting/owner/add
        // Keys
        // currency_id, name, phone, balance
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

        $ownerRequest = $request->validated();
        $ownerRequest[$role] = $agent_id;
        $this->owners
        ->create($ownerRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(OwnerRequest $request, $id){
        // /agent/accounting/owner/update/{id}
        // Keys
        // currency_id, name, phone, balance
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

        $ownerRequest = $request->validated(); 
        $this->owners
        ->where($role, $agent_id)
        ->where('id', $id)
        ->update($ownerRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/accounting/owner/delete/{id}
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

        $owner = $this->owners
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();
        if (empty($owner)) {
            return response()->json([
                'errors' => 'id is wrong'
            ], 400);
        }
        if ($owner->balance	> 0) {
            return response()->json([
                'errors' => 'Balance of owner must be zero'
            ], 403);
        }
        $owner->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
