<?php

namespace App\Http\Controllers\Api\Agent\supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\supplier\SupplierRequest;

use App\Models\SupplierAgent;

class SupplierController extends Controller
{
    public function __construct(private SupplierAgent $supplier_agent){}
    protected $supplierRequest = [
        'agent',
        'admin_name',
        'admin_phone',
        'admin_email',
        'emails',
        'phones',
    ];

    public function view(Request $request){
        $validation = Validator::make($request->all(), [ 
            'agent_id' => 'required|numeric',
            'role' => 'required|in:affilate,freelancer,agent,supplier'
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->role == 'affilate' || $request->role == 'freelancer') {    
            $supplier_agent = $this->supplier_agent 
            ->where('affilate_id', $request->agent_id)
            ->with('services')
            ->get();
        } 
        else {
            $supplier_agent = $this->supplier_agent 
            ->where('agent_id', $request->agent_id)
            ->with('services')
            ->get();
        }
        
        return response()->json([
            'supplier_agent' => $supplier_agent
        ]); 
    }

    public function create(SupplierRequest $request){
        $supplierRequest = $request->only($this->supplierRequest);
        if ($request->role == 'affilate' || $request->role == 'freelancer') {    
            $supplierRequest['affilate_id'] = $request->agent_id;
        } 
        else {
            $supplierRequest['agent_id'] = $request->agent_id;
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

    }

    public function delete($id){
        $this->supplier_agent
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
