<?php

namespace App\Http\Controllers\Api\Agent\supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\supplier\SupplierRequest;

use App\Models\SupplierAgent;
use App\Models\Service;

class SupplierController extends Controller
{
    public function __construct(private SupplierAgent $supplier_agent,
    private Service $services){}
    protected $supplierRequest = [
        'agent',
        'admin_name',
        'admin_phone',
        'admin_email',
        'emails',
        'phones',
    ];

    public function view(Request $request){
        // supplier
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
            $supplier_agent = $this->supplier_agent 
            ->where('affilate_id', $agent_id)
            ->with('services')
            ->get();
        } 
        else {
            $supplier_agent = $this->supplier_agent 
            ->where('agent_id', $agent_id)
            ->with('services')
            ->get();
        }
        $services = $this->services
        ->get();
        
        return response()->json([
            'supplier_agent' => $supplier_agent,
            'services' => $services
        ]); 
    }

    public function supplier(Request $request, $id){
        // supplier
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
            $supplier_agent = $this->supplier_agent 
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->with('services')
            ->first();
        } 
        else {
            $supplier_agent = $this->supplier_agent 
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->with('services')
            ->first();
        } 
        
        return response()->json([ 
            'service' => $services
        ]); 
    }

    public function create(SupplierRequest $request){
        // supplier/add
        // Keys
        // agent,admin_name,admin_phone,admin_email,emails,phones,services[],
        $supplierRequest = $request->only($this->supplierRequest);
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
            $supplierRequest['affilate_id'] = $agent_id;
        } 
        else {
            $supplierRequest['agent_id'] = $agent_id;
        }
        $supplier_agent = $this->supplier_agent
        ->create($supplierRequest);
        if ($request->services) {
            $services = json_decode($request->services);
            foreach ($services as $item) {
                $supplier_agent->services()->attach($item);
            }
        }
        
        return response()->json([
            'supplier_agent' => $supplier_agent
        ]);
    }

    public function modify(SupplierRequest $request, $id){
        // supplier/update/{id}
        $supplierRequest = $request->only($this->supplierRequest);
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
            $supplier_agent = $this->supplier_agent
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->first();
        } 
        else {
            $supplier_agent = $this->supplier_agent
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->first();
        }
        if (empty($supplier_agent)) {
            return response()->json([
                'faild' => 'Supplier not found'
            ], 400);
        }
        $supplier_agent->update($supplierRequest);
        if ($request->services) {
            $services = json_decode($request->services); 
            $supplier_agent->services()->sync($services); 
        }
        
        return response()->json([
            'supplier_agent' => $supplier_agent
        ]);
    }

    public function delete(Request $request, $id){
        // supplier/delete/{id}
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
            $this->supplier_agent
            ->where('id', $id)
            ->where('affilate_id', $agent_id)
            ->delete();
        }
        else{
            $this->supplier_agent
            ->where('id', $id)
            ->where('agent_id', $agent_id)
            ->delete();
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
