<?php

namespace App\Http\Controllers\Api\Agent\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\CustomerData;
use App\Models\Customer;
use App\Models\CustomerPhoneRequest;

class CustomerController extends Controller
{
    public function __construct( private CustomerData $customer_data,
    private CustomerPhoneRequest $phone_request, private Customer $customer){}

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
            ->whereHas('customer', function($query){
                $query->where('role', 'customer');
            })
            ->with(['customer' => function($query){
                $query->with('country:id,name', 'city:id,name');
            }, 'request'])
            ->get()
            ->map(function ($item) {
                $item->id = $item->customer->id;
                $item->name = $item->customer->name;
                $item->phone = $item->customer->phone;
                $item->email = $item->customer->email;
                $item->gender = $item->customer->gender;
                $item->emergency_phone = $item->customer->emergency_phone;
                $item->watts = $item->customer->watts;
                $item->image = $item->customer->image;
                
                $item->stages = $item?->request?->stages ?? null;
                $item->priority = $item?->request?->priority ?? null;
                $item->makeHidden('customer');
                $item->makeHidden('request');
                return $item;
            });
        }
        else {
            $customers = $this->customer_data
            ->where('type', 'customer')
            ->where('agent_id', $agent_id)
            ->whereHas('customer', function($query){
                $query->where('role', 'customer');
            })
            ->with(['customer' => function($query){
                $query->with('country:id,name', 'city:id,name');
            }, 'request'])
            ->get()
            ->map(function ($item) {
                $item->id = $item->customer->id;
                $item->name = $item->customer->name;
                $item->phone = $item->customer->phone;
                $item->email = $item->customer->email;
                $item->gender = $item->customer->gender;
                $item->emergency_phone = $item->customer->emergency_phone;
                $item->watts = $item->customer->watts;
                $item->image = $item->customer->image;
                
                $item->stages = $item?->request?->stages ?? null;
                $item->priority = $item?->request?->priority ?? null;
                $item->makeHidden('customer');
                $item->makeHidden('request');
                return $item;
            });
        }

        return response()->json([
            'customers' => $customers
        ]);
    }

    public function status(Request $request, $id){
        // customer/status/{id}
        // Keys
        // status
        $validation = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,suspend',
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
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $this->customer_data
        ->where('customer_id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status
        ]);
    }

    public function update(Request $request, $id){
        // customer/update/{id}
        // Keys
        // phone
        $validation = Validator::make($request->all(), [
            'phone' => 'required|unique:customers,phone',
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
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }

        $customers = $this->customer
        ->where('id', $id)
        ->first();
        $this->phone_request
        ->create([
            'customer_id' => $id,
            $role => $agent_id,
            'old_phone' => $customers->phone,
            'new_phone' => $request->phone,
        ]);

        return response()->json([
            'success' => 'You send request success'
        ]);
    }
}
