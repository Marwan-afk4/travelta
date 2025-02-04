<?php

namespace App\Http\Controllers\Api\Agent\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerData;

class LeadProfileController extends Controller
{
    public function __construct(private CustomerData $customer_data){}

    public function profile(Request $request, $id){
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
        $customer_info = $this->customer_data
        ->where('id', $id)
        ->where('');
    }
}
