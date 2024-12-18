<?php

namespace App\Http\Controllers\Api\Agent\lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\lead\LeadRequest;

use App\Models\Customer;
use App\Models\CustomerData;

class LeadController extends Controller
{
    public function __construct(private Customer $customer, 
    private CustomerData $customer_data){}
    protected $leadRequest = [
        'name',
        'phone',
        'email',
        'gender',
    ];

    public function view(Request $request){
        // /leads/leads
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
            $leads = $this->customer_data
            ->where('type', 'lead')
            ->where('affilate_id', $request->agent_id)
            ->with('customer')
            ->get();
        } 
        else {
            $leads = $this->customer_data
            ->where('type', 'lead')
            ->where('agent_id', $request->agent_id)
            ->with('customer')
            ->get();
        }
        
        return response()->json([
            'leads' => $leads->pluck('customer')
        ]);
    }

    public function leads_search(){
        // /leads/leads_search
        $leads = $this->customer_data
        ->where('type', 'lead')
        ->with('customer')
        ->get();
        
        return response()->json([
            'leads' => $leads->pluck('customer')
        ]);
    }

    public function add_lead(Request $request){
        // /leads/add_lead
        // Keys
        // customer_id, agent_id, role
        $validation = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'required|numeric',
            'role' => 'required|in:affilate,freelancer,agent,supplier'
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

        if ($request->role == 'affilate' || $request->role == 'freelancer') {        
            $customer_data = $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('affilate_id', $request->agent_id)
            ->first();
        } 
        else {
            $customer_data = $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('agent_id', $request->agent_id)
            ->first();
        }
        if (!empty($customer_data)) {
            return response()->json([
                'faild' => 'You add lead before'
            ], 400);
        }
        
        if ($request->role == 'affilate' || $request->role == 'freelancer') {        
            $this->customer_data
            ->create([
                'customer_id' => $request->customer_id,
                'affilate_id' => $request->agent_id,
            ]);
        } 
        else {
            $this->customer_data
            ->create([
                'customer_id' => $request->customer_id,
                'agent_id' => $request->agent_id,
            ]);
        }

        return response()->json([
            'success' => 'You Add lead success'
        ]);
    }

    public function create(LeadRequest $request){
        // مفيش edit احنا بنديله رسالة تأكيد بالمعلومات 
        // /leads/add
        // Keys
        // name, phone, email, gender, role, agent_id => من token
        $leadRequest = $request->only($this->leadRequest);
        $customer = $this->customer
        ->create($leadRequest);
        
        if ($request->role == 'affilate' || $request->role == 'freelancer') {        
            $this->customer_data
            ->create([
                'customer_id' => $customer->id,
                'affilate_id' => $request->agent_id,
            ]);
        } 
        else {
            $this->customer_data
            ->create([
                'customer_id' => $customer->id,
                'agent_id' => $request->agent_id,
            ]);
        }
        
        return response()->json([
            'success' => $customer
        ]);
    }

    public function delete(Request $request){
        // /leads/delete
        // Keys
        // customer_id, agent_id, role
        $validation = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'required|numeric',
            'role' => 'required|in:affilate,freelancer,agent,supplier'
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

        if ($request->role == 'affilate' || $request->role == 'freelancer') {        
            $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('affilate_id', $request->agent_id)
            ->delete();
        } 
        else {
            $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('agent_id', $request->agent_id)
            ->delete();
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
