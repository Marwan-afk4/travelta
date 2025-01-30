<?php

namespace App\Http\Controllers\Api\Agent\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\HotelRequestResource;
use App\Http\Resources\BusRequestResource;
use App\Http\Resources\FlightRequestResource;
use App\Http\Resources\TourRequestResource;
use App\Http\Resources\VisaRequestResource;

use App\Models\CustomerData;
use App\Models\AdminAgent;
use App\Models\Service;
use App\Models\CurrencyAgent;
use App\Models\Country;
use App\Models\RequestBooking;

class RequestListsController extends Controller
{
    public function __construct(private CustomerData $customer_data, 
    private AdminAgent $admin_agents, private Service $services,
    private CurrencyAgent $currency, private Country $countries,
    private RequestBooking $request_booking){}

    public function lists(Request $request){
        // agent/request/lists
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
        $customers = $this->customer_data
        ->where($role, $agent_id)
        ->with('customer')
        ->get()
        ->map(function ($item) {
            $item->id = $item->customer->id; // Set customer_data.id to customers.id
            $item->makeHidden('customer');
            return $item;
        });
        $admins_agent = $this->admin_agents
        ->where($role, $agent_id)
        ->get();
        $services = $this->services
        ->get();
        $currencies = $this->currency
        ->where($role, $agent_id)
        ->get();
        $priority = ['Low', 'Normal', 'High'];
        $stages = ['Pending', 'Price quotation', 'Negotiation', 'Won', 'Won Canceled', 'Lost'];
        $countries = $this->countries
        ->get();

        return response()->json([
            'customers' => $customers,
            'admins_agent' => $admins_agent,
            'services' => $services,
            'currencies' => $currencies,
            'priority' => $priority,
            'stages' => $stages,
            'countries' => $countries,
        ]);
    }

    public function view(Request $request){
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
        $hotels = $this->request_booking
        ->where($role, $agent_id)
        ->with('hotel', 'customer', 'admin_agent', 'currency')
        ->whereHas('hotel')
        ->get();
        $buses = $this->request_booking
        ->where($role, $agent_id)
        ->with('bus', 'customer', 'admin_agent', 'currency')
        ->whereHas('bus')
        ->get();
        $flights = $this->request_booking
        ->where($role, $agent_id)
        ->with('flight', 'customer', 'admin_agent', 'currency')
        ->whereHas('flight')
        ->get();
        $visas = $this->request_booking
        ->where($role, $agent_id)
        ->with('flight', 'customer', 'admin_agent', 'currency')
        ->whereHas('flight')
        ->get();

        $hotels = HotelRequestResource::collection($hotels);
        $buses = BusRequestResource::collection($buses);
        $flights = FlightRequestResource::collection($flights);
        $visas = VisaRequestResource::collection($visas);

        return response()->json([
            'hotels' => $hotels,
            'buses' => $buses,
            'flights' => $flights,
            'visas' => $visas,
            'hotels' => $hotels,
        ]);
    }
}
