<?php

namespace App\Http\Controllers\Api\Agent\lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\trait\image;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\lead\LeadRequest;
use Illuminate\Validation\Rule;
use App\Exports\ExportLeads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Leads;

use App\Models\Customer;
use App\Models\CustomerData;

use App\Models\CustomerSource;
use App\Models\HrmEmployee;
use App\Models\Service;
use App\Models\Nationality;
use App\Models\Country;
use App\Models\City;

class LeadController extends Controller
{
    public function __construct(private Customer $customer, 
    private CustomerData $customer_data, private CustomerSource $sources, 
    private HrmEmployee $aget_sales, private Service $services
    , private Nationality $nationalities, private Country $countries
    , private City $cities){} 
    use image;
    protected $leadRequest = [
        'name',
        'phone',
        'email',
        'gender',
        'emergency_phone',

        'watts',
        'service_id',
        'nationality_id',
        'country_id',
        'city_id',
        'status',
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
            ->with(['customer', 'source:id,source', 'agent_sales:name,department_id' => function($query){
                $query->with('department:id,name');
            }, 'service:id,service_name', 'nationality:id,name', 'country:id,name', 
            'city:id,name', 'request'])
            ->get()
            ->map(function ($item) {
                if ($item->customer->role == 'customer') {
                    $item->name = $item->customer->name;
                    $item->phone = $item->customer->phone;
                    $item->email = $item->customer->email;
                    $item->gender = $item->customer->gender;
                    $item->emergency_phone = $item->customer->emergency_phone;
                    $item->watts = $item->customer->watts;
                    $item->image = $item->customer->image;
                }
                $item->stages = $item?->request?->stages ?? null;
                $item->priority = $item?->request?->priority ?? null;
                $item->makeHidden('customer');
                $item->makeHidden('request');
                return $item;
            });
        } 
        else {
            $leads = $this->customer_data
            ->where('type', 'lead')
            ->where('agent_id', $agent_id)
            ->with([
                'customer',
                'source:id,source',
                'agent_sales' => function($query) {
                    $query->select('id', 'name', 'department_id')->with('department:id,name');
                },
                'service:id,service_name',
                'nationality:id,name',
                'country:id,name',
                'city:id,name',
                'request'
            ])
            ->get()
            ->map(function ($item) {
                if ($item->customer->role == 'customer') {
                    $item->name = $item->customer->name;
                    $item->phone = $item->customer->phone;
                    $item->email = $item->customer->email;
                    $item->gender = $item->customer->gender;
                    $item->emergency_phone = $item->customer->emergency_phone;
                    $item->watts = $item->customer->watts;
                    $item->image = $item->customer->image;
                }
                $item->stages = $item?->request?->stages ?? null;
                $item->priority = $item?->request?->priority ?? null;
                $item->makeHidden('customer');
                $item->makeHidden('request');
                return $item;
            });
        }
        
        return response()->json([
            'leads' => $leads
        ]);
    }

    public function lists(Request $request){
        // /agent/leads/lists
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
        else{
            $role = 'agent_id'; 
        }

        $sources = $this->sources
        ->where('status', 1)
        ->get();
        $aget_sales = $this->aget_sales
        ->where($role, $agent_id)
        ->where('status', 1)
        ->get();
        $services = $this->services
        ->get();
        $nationalities = $this->nationalities
        ->get();
        $countries = $this->countries
        ->get();
        $cities = $this->cities
        ->get();
        
        return response()->json([
            'sources' => $sources,
            'aget_sales' => $aget_sales,
            'services' => $services,
            'nationalities' => $nationalities,
            'countries' => $countries,
            'cities' => $cities,
        ]);
    }

    public function export_excel(){
        // /agent/leads/export_excel
        return Excel::download(new ExportLeads, 'leads_template.xlsx');
    }

    public function import_excel(){
        // /agent/leads/import_excel
        $validation = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,csv',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        Excel::import(new Leads, $request->file('file'));

        return response()->json(['message' => 'File uploaded successfully']);
    }

    public function status(Request $request, $id){
        // /leads/status/{id}
        $validation = Validator::make($request->all(), [
            'status' => 'required|boolean',
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
        else{
            $role = 'agent_id'; 
        }
        $this->customer_data
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active': 'banned'
        ]);
    }

    public function leads_search(Request $request){
        // /leads/leads_search
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $user_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $user_id = $request->user()->agent_id;
        }
        else{
            $user_id = $request->user()->id;
        } 
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
        // customer_id, source_id, agent_sales_id
        $validation = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'source_id' => 'required|exists:customer_sources,id',
            'agent_sales_id' => 'required|exists:hrm_employees,id',
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
        $leadRequest = [
                'customer_id' => $request->customer_id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'gender' => $customer->gender,
                'watts' => $customer->watts,
                'source_id' => $request->source_id,
                'agent_sales_id' => $request->agent_sales_id,
                'service_id' => $customer->service_id,
                'nationality_id' => $customer->nationality_id,
                'country_id' => $customer->country_id,
                'city_id' => $customer->city_id,
                'status' => $customer->status, 
                'image' => $customer->image, 
            ];
        // if (!empty($request->image)) {
        //     $image = $this->storeBase64Image($request->image, 'agent/lead/image');
        //     $leadRequest['image'] = $image;
        // }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $leadRequest['affilate_id'] = $agent_id;
            $this->customer_data
            ->create($leadRequest);
        } 
        else {
            $leadRequest['agent_id'] = $agent_id;
            $this->customer_data
            ->create($leadRequest);
        }

        return response()->json([
            'success' => 'You Add lead success'
        ]);
    }

    public function create(LeadRequest $request){ 
        // /leads/add
        // Keys
        // name, phone, email, gender
        // image, watts, source_id, agent_sales_id, service_id,
        // nationality_id, country_id, city_id, status      
        $validation = Validator::make($request->all(), [
            'phone' => ['unique:customers,phone'],
            'email' => ['unique:customers,email'],
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $leadRequest = $request->only($this->leadRequest);
        $customer = $this->customer
        ->where('phone', $request->phone)
        ->first(); 
        if (empty($customer)) {
            if (!empty($request->image)) {
                $image = $this->storeBase64Image($request->image, 'agent/lead/image');
                $leadRequest['image'] = $image;
            }
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
         
        $customer_arr = [
            'customer_id' => $customer->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'gender' => $request->gender ?? null,
            'watts' => $request->watts ?? null,
            'source_id' => $request->source_id ?? null,
            'agent_sales_id' => $request->agent_sales_id ?? null,
            'service_id' => $request->service_id ?? null,
            'nationality_id' => $request->nationality_id ?? null,
            'country_id' => $request->country_id ?? null,
            'city_id' => $request->city_id ?? null,
            'status' => $request->status ?? null,
            'image' => $customer->image ?? null,
        ];
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $customer_arr['affilate_id'] = $agent_id;
            $this->customer_data
            ->create($customer_arr);
        } 
        else { 
            $customer_arr['agent_id'] = $agent_id;
            $this->customer_data
            ->create($customer_arr);
        }
        
        return response()->json([
            'success' => $customer
        ]);
    }

    public function modify(LeadRequest $request, $id){ 
        // /leads/update/{id}
        // Keys
        // name, phone, email, gender,
        // image, watts, source_id, agent_sales_id, service_id,
        // nationality_id, country_id, city_id, status
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
        $leadRequest = $request->only($this->leadRequest);
        $customer = $this->customer_data
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        if (empty($customer)) {
            return response()->json([
                'errors' => 'lead not found'
            ], 400);
        }
        $customer_arr = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email ?? null,
            'gender' => $request->gender ?? null,
            'watts' => $request->watts ?? null,
            'source_id' => $request->source_id ?? null,
            'agent_sales_id' => $request->agent_sales_id ?? null,
            'service_id' => $request->service_id ?? null,
            'nationality_id' => $request->nationality_id ?? null,
            'country_id' => $request->country_id ?? null,
            'city_id' => $request->city_id ?? null,
            'status' => $request->status ?? null, 
        ];
        if (!empty($request->image)) {
            $image = $this->storeBase64Image($request->image, 'agent/lead/image');
            $leadRequest['image'] = $image;
            $customer_arr['image'] = $image;
            $this->deleteImage($customer->image);
        }
        if ($customer->phone != $request->phone) {
            $parent_customer = $this->customer
            ->create($leadRequest);
            $customer_arr['customer_id'] = $parent_customer->id;
        }
        $customer
        ->update($customer_arr);
        
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
            $customer = $this->customer_data
            ->where('customer_id', $id)
            ->where('affilate_id', $agent_id)
            ->first();
        } 
        else {
            $customer = $this->customer_data
            ->where('customer_id', $id)
            ->where('agent_id', $agent_id)
            ->first();
        }
        if (!empty($customer)) {
            $this->deleteImage($customer->image);
            $customer->delete();
        }

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
