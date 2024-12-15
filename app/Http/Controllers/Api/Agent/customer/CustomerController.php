<?php

namespace App\Http\Controllers\Api\Agent\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerData;

class CustomerController extends Controller
{
    public function __construct( private CustomerData $customer_data){}

    public function view(Request $request){
        // customer
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('affilate_id', $request->user()->id)
            ->with('customer')
            ->get();
        } 
        else {
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('agent_id', $request->user()->id)
            ->with('customer')
            ->get();
        }
        
        return response()->json([
            'customers' => $customers->pluck('customer')
        ]);

    }
}
