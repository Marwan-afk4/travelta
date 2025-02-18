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
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('affilate_id', $agent_id)
            ->with('customer')
            ->get();
        } 
        else {
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('agent_id', $agent_id)
            ->with('customer')
            ->get();
        }

        return response()->json([
            'customers' => $customers->pluck('customer')
        ]);
    }
}
