<?php

namespace App\Http\Controllers\Api\Agent\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\HotelRequestResource;
use App\Http\Resources\BusRequestResource;
use App\Http\Resources\FlightRequestResource;
use App\Http\Resources\TourRequestResource;
use App\Http\Resources\VisaRequestResource;
use App\Http\Resources\BookingRequestResource;

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
        // agent/request
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
        ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
        ->whereHas('hotel')
        ->get();
        $buses = $this->request_booking
        ->where($role, $agent_id)
        ->with('bus', 'customer', 'admin_agent', 'currency')
        ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
        ->whereHas('bus')
        ->get();
        $flights = $this->request_booking
        ->where($role, $agent_id)
        ->with('flight', 'customer', 'admin_agent', 'currency')
        ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
        ->whereHas('flight')
        ->get();
        $visas = $this->request_booking
        ->where($role, $agent_id)
        ->with('visa', 'customer', 'admin_agent', 'currency')
        ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
        ->whereHas('visa')
        ->get();
        $tours = $this->request_booking
        ->where($role, $agent_id)
        ->with(['tour' => function($query) {
            return $query->with('bus', 'hotel');
        }, 'customer', 'admin_agent', 'currency'])
        ->orderByRaw("FIELD(priority, 'High', 'Normal', 'Low')")
        ->whereHas('tour')
        ->get();

        $hotels = HotelRequestResource::collection($hotels);
        $buses = BusRequestResource::collection($buses);
        $flights = FlightRequestResource::collection($flights);
        $visas = VisaRequestResource::collection($visas);
        $tours = TourRequestResource::collection($tours); 
        $current = [
            'hotels' => array_values($hotels->whereIn('stages', ['Pending', 'Price quotation', 'Negotiation'])->toArray()),
            'buses' => array_values($buses->whereIn('stages', ['Pending', 'Price quotation', 'Negotiation'])->toArray()),
            'flights' => array_values($flights->whereIn('stages', ['Pending', 'Price quotation', 'Negotiation'])->toArray()),
            'visas' => array_values($visas->whereIn('stages', ['Pending', 'Price quotation', 'Negotiation'])->toArray()),
            'tours' => array_values($tours->whereIn('stages', ['Pending', 'Price quotation', 'Negotiation'])->toArray()),
        ];
        $history = [
            'hotels' => array_values($hotels->whereIn('stages', ['Won', 'Won Canceled', 'Lost'])->toArray()),
            'buses' => array_values($buses->whereIn('stages', ['Won', 'Won Canceled', 'Lost'])->toArray()),
            'flights' => array_values($flights->whereIn('stages', ['Won', 'Won Canceled', 'Lost'])->toArray()),
            'visas' => array_values($visas->whereIn('stages', ['Won', 'Won Canceled', 'Lost'])->toArray()),
            'tours' => array_values($tours->whereIn('stages', ['Won', 'Won Canceled', 'Lost'])->toArray()),
        ];
        return response()->json([
            'current' => $current,
            'history' => $history, 
        ]);
    }

    public function request_item(Request $request, $id){
        // /agent/request/item/{id}
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
        $request_booking = $this->request_booking
        ->where('id', $id)
        ->where($role, $agent_id)
        ->with(['customer', 'admin_agent', 'currency',
        'service', 'adults', 'children', 'hotel', 'bus', 
        'flight', 'tour' => function($query){
            return $query->with('bus', 'hotel');
        }, 'visa', 'stage_data'])
        ->first();
        
        $request_booking = [
            'id' => $request_booking->id ?? null,
            'to_name' => $request_booking->customer?->name ?? null, 
            'to_phone' => $request_booking->customer?->phone ?? null,
            'agent' => $request_booking->admin_agent?->name ?? null,
            'currecy' => $request_booking->currency->name ?? null,
            'service' => $request_booking->service->service_name ?? null,
            'adults' => $request_booking->adults ?? null,
            'children' => $request_booking->children ?? null,
            'hotel' => $request_booking->hotel ?? null,
            'bus' => $request_booking->bus ?? null,
            'flight' => $request_booking->flight ?? null,
            'visa' => $request_booking->visa ?? null,
            'tour' => $request_booking->expected_tour ?? null,

            'expected_revenue' => $request_booking->expected_revenue ?? null,
            'priority' => $request_booking->priority ?? null,
            'stages' => $request_booking->stages ?? null,
            'stage_data' => $request_booking->stage_data ?? null,
        ];
        return response()->json([
            'request' => $request_booking
        ]); 
    }
}
