<?php

namespace App\Http\Controllers\Api\Agent\manual_booking;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\CustomerData;
use App\Models\SupplierAgent;
use App\Models\Service;

class ManualBookingController extends Controller
{
    public function __construct(private City $cities, private Country $contries,
    private CustomerData $customer_data, private SupplierAgent $supplier_agent,
    private Service $services){}

    public function lists(){
        $cities = $this->cities
        ->get();
        $contries = $this->contries
        ->get();
        $services = $this->services
        ->get();

        return response()->json([
            'cities' => $cities,
            'contries' => $contries,
            'services' => $services,
        ]);
    }

    public function to_b2_filter(Request $request){
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
            ->where('affilate_id', $agent_id)
            ->with('customer')
            ->get();
            $suppliers = $this->supplier_agent
            ->select('id', 'agent')
            ->where('affilate_id', $agent_id)
            ->get();
        }
        else{
            $customers = $this->customer_data
            ->where('agent_id', $agent_id)
            ->with('customer')
            ->get();
            $suppliers = $this->supplier_agent
            ->select('id', 'agent')
            ->where('agent_id', $agent_id)
            ->get();
        }
        $customers = $customers->pluck('customer')->select('id', 'name', 'phone');

        return response()->json([
            'customers' => $customers,
            'suppliers' => $suppliers,
        ]);
    }

    public function from_supplier(Request $request){
        $validation = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
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
            $service = $this->services
            ->where('id', $request->service_id)
            ->with(['suppliers' => function($query) use($agent_id){
                $query->where('affilate_id', $agent_id);
            }])
            ->first();
        }
        else{
            $service = $this->services
            ->where('id', $request->service_id)
            ->with(['suppliers' => function($query) use($agent_id){
                $query->where('agent_id', $agent_id);
            }])
            ->first();
        }
        $service = $service->suppliers->select('id', 'agent');

        return response()->json([
            'service' => $service,
        ]);
    }
}
