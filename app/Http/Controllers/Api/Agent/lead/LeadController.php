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
        'emergency_phone',
    ];

    public function view(Request $request){
        // /leads
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
            $leads = $this->customer_data
            ->where('type', 'lead')
            ->where('affilate_id', $agent_id)
            ->with('customer')
            ->get()
            ->map(function ($item) {
                $item->id = $item->customer->id; // Set customer_data.id to customers.id
                $item->makeHidden('customer');
                return $item;
            });
        } 
        else {
            $leads = $this->customer_data
            ->where('type', 'lead')
            ->where('agent_id', $agent_id)
            ->with('customer')
            ->get()
            ->map(function ($item) {
                $item->id = $item->customer->id; // Set customer_data.id to customers.id
                $item->makeHidden('customer');
                return $item;
            });
        }
        
        return response()->json([
            'leads' => $leads
        ]);
    }

    public function leads_search(){
        // /leads/leads_search
        $user_id = auth()->user()->id;
        $role = auth()->user()->role == 'freelancer' ||
        auth()->user()->role == 'affilate' ? 'affilate_id' :'agent_id';
        $leads = $this->customer
        ->whereDoesntHave('agent_customer', function($query) use($user_id, $role){
            $query->where($role, $user_id);
        })
        ->get();
        
        return response()->json([
            'leads' => $leads
        ]);
    }

    public function add_lead(Request $request){
        // /leads/add_lead
        // Keys
        // customer_id
        $validation = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

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
            $customer_data = $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('affilate_id', $agent_id)
            ->first();
        } 
        else {
            $customer_data = $this->customer_data
            ->where('customer_id', $request->customer_id)
            ->where('agent_id', $agent_id)
            ->first();
        }
        if (!empty($customer_data)) {
            return response()->json([
                'faild' => 'You add lead before'
            ], 400);
        }
        $customer = $this->customer
        ->where('id', $request->customer_id)
        ->first();
        
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {        
            $this->customer_data
            ->create([
                'customer_id' => $request->customer_id,
                'affilate_id' => $agent_id,
                'name' => $customer->name,
                'phone' => $customer->phone,
            ]);
        } 
        else {
            $this->customer_data
            ->create([
                'customer_id' => $request->customer_id,
                'agent_id' => $agent_id,
                'name' => $customer->name,
                'phone' => $customer->phone,
            ]);
        }

        return response()->json([
            'success' => 'You Add lead success'
        ]);
    }

    public function create(LeadRequest $request){ 
        // /leads/add
        // Keys
        // name, phone, email, gender
        $leadRequest = $request->only($this->leadRequest);
        $customer = $this->customer
        ->where('phone', $request->phone)
        ->first();
        if (empty($customer)) {
            $customer = $this->customer
            ->create($leadRequest);
        }
        
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
            $this->customer_data
            ->create([
                'customer_id' => $customer->id,
                'affilate_id' => $agent_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email ?? null,
                'gender' => $request->gender ?? null,
            ]);
        } 
        else {
            $this->customer_data
            ->create([
                'customer_id' => $customer->id,
                'agent_id' => $agent_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email ?? null,
                'gender' => $request->gender ?? null,
            ]);
        }
        
        return response()->json([
            'success' => $customer
        ]);
    }

    public function modify(LeadRequest $request, $id){ 
        // /leads/update/{id}
        // Keys
        // name, phone, email, gender
        $leadRequest = $request->only($this->leadRequest);
        $customer = $this->customer
        ->where('id', $id)
        ->first();
        if (empty($customer)) {
            return response()->json([
                'errors' => 'lead not found'
            ], 400);
        }
        
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
        $customer
        ->update([ 
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'gender' => $request->gender ?? null,
        ]);
        
        return response()->json([
            'success' => $customer
        ]);
    }

    // public function modify(LeadRequest $request){ 
    //     // /leads/update
    //     // Keys
    //     // name, phone, email, gender
    //     $leadRequest = $request->only($this->leadRequest);
    //     $customer = $this->customer
    //     ->where('phone', $request->phone)
    //     ->first();
    //     if (empty($customer)) {
    //         $customer = $this->customer
    //         ->create($leadRequest);
    //     }
        
    //     if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
    //         $agent_id = $request->user()->affilate_id;
    //     }
    //     elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
    //         $agent_id = $request->user()->agent_id;
    //     }
    //     else{
    //         $agent_id = $request->user()->id;
    //     }
    //     if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {        
    //         $this->customer_data
    //         ->create([
    //             'customer_id' => $customer->id,
    //             'affilate_id' => $agent_id,
    //             'name' => $request->name,
    //             'phone' => $request->phone,
    //         ]);
    //     } 
    //     else {
    //         $this->customer_data
    //         ->create([
    //             'customer_id' => $customer->id,
    //             'agent_id' => $agent_id,
    //             'name' => $request->name,
    //             'phone' => $request->phone,
    //         ]);
    //     }
        
    //     return response()->json([
    //         'success' => $customer
    //     ]);
    // }

    public function delete(Request $request, $id){
        // /leads/delete/{id}
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
            $this->customer_data
            ->where('customer_id', $id)
            ->where('affilate_id', $agent_id)
            ->delete();
        } 
        else {
            $this->customer_data
            ->where('customer_id', $id)
            ->where('agent_id', $agent_id)
            ->delete();
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
