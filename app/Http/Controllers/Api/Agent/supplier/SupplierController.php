<?php

namespace App\Http\Controllers\Api\Agent\supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Http\Requests\api\agent\supplier\SupplierRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SupplierImport;
use Illuminate\Support\Facades\Validator;

use App\Models\SupplierAgent;
use App\Models\SupplierBalance;
use App\Models\SupplierAgentService;
use App\Models\Service;

class SupplierController extends Controller
{
    public function __construct(private SupplierAgent $supplier_agent,
    private Service $services, private SupplierBalance $supplier_balance,
    private SupplierAgentService $supplier_service){}
    protected $supplierRequest = [
        'agent',
        'admin_name',
        'admin_phone',
        'admin_email',
        'emails',
        'phones',
        'emergency_phone',
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

    public function import_excel(Request $request){
        // /agent/supplier/import_excel
        $validation = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,csv',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        Excel::import(new SupplierImport, $request->file('file'));

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function supplier(Request $request, $id){
        // supplier/item/{id}
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
            'supplier_agent' => $supplier_agent, 
        ]); 
    }

    public function create(SupplierRequest $request){
        // supplier/add
        // Keys
        // agent,admin_name,admin_phone,admin_email,emails[],phones[],services[{id, description}],
        // balances[currency_id,balance]
        $supplierRequest = $request->only($this->supplierRequest);
        $supplierRequest['emails'] = is_string($request->emails) ?$request->emails:
        json_encode($request->emails);
        $supplierRequest['phones'] = is_string($request->phones) ?$request->phones:
        json_encode($request->phones);
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
            $services = is_string($request->services) ? json_decode($request->services) :
            $request->services;
            foreach ($services as $item) {
                $this->supplier_service
                ->create([
                    'supplier_agent_id' => $supplier_agent->id,
                    'service_id' => $item->id ?? $item['id'],
                    'description' => $item->description ?? $item['description'],
                ]);
            }
        }
        if ($request->balances) {
            $balances = $request->balances;
            foreach ($balances as $item) {
                $this->supplier_balance
                ->create([
                    'currency_id' => $item['currency_id'],
                    'balance' => $item['balance'],
                    'supplier_id' => $supplier_agent->id
                ]);
            }
        }
        
        return response()->json([
            'supplier_agent' => $supplier_agent
        ]);
    }

    public function modify(SupplierRequest $request, $id){
        // supplier/update/{id}
        $supplierRequest = $request->only($this->supplierRequest); 
        $supplierRequest['emails'] = is_string($request->emails) ?$request->emails:
        json_encode($request->emails);
        $supplierRequest['phones'] = is_string($request->phones) ?$request->phones:
        json_encode($request->phones);
        $supplierRequest['services'] = is_string($request->services) ?$request->services:
        json_encode($request->services);
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
            $services = is_string($request->services) ? json_decode($request->services) :
            $request->services;
            $supplier_agent->services()->sync([]); 
            foreach ($services as $item) {
                $this->supplier_service
                ->create([
                    'supplier_agent_id' => $supplier_agent->id,
                    'service_id' => $item->id ?? $item['id'],
                    'description' => $item->description ?? $item['description'],
                ]);
            }
        }
        $this->supplier_balance
        ->where('supplier_id', $supplier_agent->id)
        ->delete();
        if ($request->balances) {
            $balances = $request->balances;
            foreach ($balances as $item) {
                $this->supplier_balance
                ->create([
                    'currency_id' => $item['currency_id'],
                    'balance' => $item['balance'],
                    'supplier_id' => $supplier_agent->id
                ]);
            }
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
