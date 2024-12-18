<?php

namespace App\Http\Controllers\Api\Agent\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\CustomerData;

class CustomerController extends Controller
{
    public function __construct( private CustomerData $customer_data){}

    public function view(Request $request){
        // customer   
        // Keys
        // agent_id, role
        $validation = Validator::make($request->all(), [ 
            'agent_id' => 'required|numeric',
            'role' => 'required|in:affilate,freelancer,agent,supplier'
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->role == 'affilate' || $request->role == 'freelancer') {    
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('affilate_id', $request->agent_id)
            ->with('customer')
            ->get();
        } 
        else {
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('agent_id', $request->agent_id)
            ->with('customer')
            ->get();
        }
        
        return response()->json([
            'customers' => $customers->pluck('customer')
        ]);

    }
}
