<?php

namespace App\Http\Controllers\Api\Agent\manual_booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\manuel_booking\ManuelBookingRequest;
use App\Http\Requests\api\agent\manuel_booking\CartBookingRequest;
use Illuminate\Support\Str;
use App\Http\Resources\ManuelCartResource;

use App\Models\CustomerData;
use App\Models\SupplierAgent;
use App\Models\Service;
use App\Models\City;
use App\Models\Country;
use App\Models\Tax;
use App\Models\ManuelBooking;
use App\Models\ManuelBus;
use App\Models\ManuelFlight;
use App\Models\ManuelHotel;
use App\Models\ManuelTour;
use App\Models\ManuelVisa;
use App\Models\ManuelTourBus;
use App\Models\ManuelTourHotel;
use App\Models\CurrencyAgent;
use App\Models\Adult;
use App\Models\Child;
use App\Models\ManuelCart;
use App\Models\PaymentsCart;
use App\Models\ManuelDataCart;
use App\Models\Customer;
use App\Models\FinantiolAcounting;
use App\trait\image;

class ManualBookingController extends Controller
{
    use image;
    public function __construct(private City $cities, private Country $contries,
    private CustomerData $customer_data, private SupplierAgent $supplier_agent,
    private Service $services, private Tax $taxes, private ManuelBooking $manuel_booking,
    private ManuelBus $manuel_bus, private ManuelFlight $manuel_flight, 
    private ManuelHotel $manuel_hotel, private ManuelTour $manuel_tour, 
    private ManuelVisa $manuel_visa, private ManuelTourBus $manuel_tour_bus,
    private ManuelTourHotel $manuel_tour_hotel, private CurrencyAgent $currency,
    private Adult $adults, private Child $child, private ManuelCart $manuel_cart,
    private PaymentsCart $payments_cart, private ManuelDataCart $manuel_data_cart,
    private Customer $customers, private FinantiolAcounting $financial_accounting){}
    
    protected $hotelRequest = [
        'check_in',
        'check_out',
        'nights',
        'hotel_name',
        'room_type',
        'room_quantity',
        'adults',
        'childreen',
    ];
    protected $busRequest = [
        'from',
        'to',
        'departure',
        'arrival',
        'adults',
        'childreen',
        'adult_price',
        'child_price',
        'bus',
        'bus_number',
        'driver_phone'
    ];
    protected $visaRequest = [
        'country',
        'travel_date',
        'appointment_date',
        'notes',
        'number',
        'customers',
    ];
    protected $flightRequest = [
        'type',
        'direction',
        'from_to',
        'departure',
        'arrival',
        'class',
        'adults',
        'childreen',
        'infants',
        'airline',
        'ticket_number',
        'adult_price',
        'child_price',
        'ref_pnr',
    ];
    protected $tourRequest = [
        'tour',
        'type',
        'adult_price',
        'child_price',
        'adults',
        'childreen',
    ];
    protected $manuelRequest = [
        'to_supplier_id',
        'to_customer_id',
        'from_supplier_id',
        'from_service_id',
        'cost' ,
        'price' ,
        'currency_id',
        'tax_type',
        'total_price' ,
        'country_id',
        'city_id',
        'mark_up' ,
        'mark_up_type'
    ];

    public function lists(Request $request){
        // https://travelta.online/agent/manual_booking/lists
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        $cities = $this->cities
        ->get();
        $contries = $this->contries
        ->get();
        $services = $this->services
        ->get();
        $financial_accounting = $this->financial_accounting
        ->get();
        $adult_title = [
            'MR',
            'MISS',
            'MRS',
        ];
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $currencies = $this->currency
            ->where('affilate_id', $agent_id)
            ->get();
        } 
        else {
            $currencies = $this->currency
            ->where('agent_id', $agent_id)
            ->get();
        }

        return response()->json([
            'cities' => $cities,
            'contries' => $contries,
            'services' => $services,
            'currencies' => $currencies,
            'adult_title' => $adult_title,
            'financial_accounting' => $financial_accounting
        ]);
    }


    public function mobile_lists(Request $request){
        // https://travelta.online/agent/manual_booking/mobile_lists
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        $cities = $this->cities
        ->get();
        $contries = $this->contries
        ->get();
        $financial_accounting = $this->financial_accounting
        ->get();
        $adult_title = [
            'MR',
            'MISS',
            'MRS',
        ];
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }
        $currencies = $this->currency
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get(); 
        $services = $this->services
        ->with(['suppliers' => function($query){
            $query->select('supplier_agents.id', 'agent');
        }])
        ->whereHas('suppliers', function($query) use($role, $agent_id){
            $query->where($role, $agent_id);
        })
        ->get();
        $taxes = $this->taxes
        ->get();
        $customers = $this->customer_data
        ->where($role, $agent_id)
        ->with('customer')
        ->get()
        ->pluck('customer');
        $suppliers = $this->supplier_agent
        ->select('id', 'agent')
        ->where($role, $agent_id)
        ->get();

        return response()->json([
            'cities' => $cities,
            'contries' => $contries,
            'services' => $services,
            'currencies' => $currencies,
            'adult_title' => $adult_title,
            'financial_accounting' => $financial_accounting,
            'taxes' => $taxes,
            'customers' => $customers,
            'suppliers' => $suppliers,
        ]);
    }

    public function to_b2_filter(Request $request){
        // https://travelta.online/agent/manual_booking/supplier_customer
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
        // https://travelta.online/agent/manual_booking/service_supplier
        // Keys
        // service_id
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
        $supplier = $service->suppliers->select('id', 'agent');

        return response()->json([
            'supplier' => $supplier,
        ]);
    }

    public function from_taxes(Request $request){
        // https://travelta.online/agent/manual_booking/taxes
        // Keys
        // country_id
        $validation = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $taxes = $this->taxes
        ->where('country_id', $request->country_id)
        ->get();

        return response()->json([
            'taxes' => $taxes
        ]);
    }

    public function cart(ManuelBookingRequest $request){
        $total = $request->total_price;
            $service = $this->services
            ->where('id', $request->from_service_id)
            ->first()->service_name;
            if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
                $validation = Validator::make($request->all(), [
                    'check_in' => 'required|date',
                    'check_out' => 'required|date',
                    'nights' => 'required|numeric',
                    'hotel_name' => 'required',
                    'room_type' => 'required',
                    'room_quantity' => 'required|numeric',
                    'adults' => 'required|numeric',
                    'childreen' => 'required|numeric',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
            }
            elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
                $validation = Validator::make($request->all(), [
                    'from' => 'required',
                    'to' => 'required',
                    'departure' => 'required|date',
                    'arrival' => 'required|date',
                    'adults' => 'required|numeric',
                    'childreen' => 'required|numeric',
                    'adult_price' => 'required|numeric',
                    'child_price' => 'required|numeric',
                    'bus' => 'required',
                    'bus_number' => 'required',
                    'driver_phone' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
            }
            elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
                $validation = Validator::make($request->all(), [
                    'country' => 'required',
                    'travel_date' => 'required|date', 
                    'appointment_date' => 'date',
                    'number' => 'required|numeric', 
                    'customers' => 'required', 
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
            }
            elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
                $validation = Validator::make($request->all(), [
                    'type' => 'in:domestic,international',
                    'direction' => 'in:one_way,round_trip,multi_city',
                    'departure' => 'date',
                    'arrival' => 'date',
                    'adults' => 'numeric',
                    'childreen' => 'numeric',
                    'infants' => 'numeric',
                    'adult_price' => 'numeric',
                    'child_price' => 'numeric',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
            }
            elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
                $validation = Validator::make($request->all(), [
                    'tour' => 'required',
                    'type' => 'required|in:domestic,international',
                    'adult_price' => 'numeric',
                    'child_price' => 'numeric',
                    'adults' => 'numeric',
                    'childreen' => 'numeric',
                    'flight_date' => 'date',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
            }
            
            $manuel_data_cart = $this->manuel_data_cart
            ->create([
                'cart' => json_encode($request->all())
            ]);

            return response()->json([
                'cart_id' => $manuel_data_cart->id,
                'total' => $total,
            ]);
    }

    public function cart_data($id){
        $manuel_data_cart = $this->manuel_data_cart
        ->where('id', $id)
        ->first();
        $manuel_data_cart = json_decode($manuel_data_cart->cart);
        if (!empty($manuel_data_cart->to_supplier_id)) {
            $to_client = $this->supplier_agent
            ->where('id', $manuel_data_cart->to_supplier_id)
            ->first();
        } else {   
            $to_client = $this->customers
            ->where('id', $manuel_data_cart->to_customer_id)
            ->first();
        }

        if (empty($manuel_data_cart)) {
            return response()->json([
                'errors' => 'id is wrong'
            ], 400);
        }
        $service = $this->services->where('id', $manuel_data_cart->from_service_id)->first()->service_name ?? null;
        $hotel = null;
        $bus = null;
        $visa = null;
        $flight = null;
        $tour = null;
        if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
            $hotel = [
                "check_in" => $manuel_data_cart->check_in ?? null,
                "check_out" => $manuel_data_cart->check_out ?? null,
                "hotel_name" => $manuel_data_cart->hotel_name ?? null, 
                "nights" => $manuel_data_cart->nights ?? null,
                "room_type" => $manuel_data_cart->room_type ?? null,
                "room_quantity" => $manuel_data_cart->room_quantity ?? null,
                "childreen" =>  $manuel_data_cart->childreen ?? null,
                "adults" =>  $manuel_data_cart->adults ?? null,
                "adults_data" =>  json_decode($manuel_data_cart->adults_data ?? '[]') ?? [],
                "children_data" =>  json_decode($manuel_data_cart->children_data ?? '[]') ?? [],
            ];
        }
        elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
            $bus = [
                "from" => $manuel_data_cart->from ?? null,
                "to" => $manuel_data_cart->to ?? null,  
                "bus" => $manuel_data_cart->bus ?? null,
                "departure" =>  $manuel_data_cart->departure ?? null,
                "arrival" => $manuel_data_cart->arrival ?? null,  
                "adults" => $manuel_data_cart->adults ?? null, 
                "childreen" => $manuel_data_cart->childreen ?? null, 
                "adult_price" => $manuel_data_cart->adult_price ?? null, 
                "child_price" => $manuel_data_cart->child_price ?? null, 
                "bus" => $manuel_data_cart->bus ?? null,
                "bus_number" => $manuel_data_cart->bus_number ?? null,
                "driver_phone" => $manuel_data_cart->driver_phone ?? null, 
                "adults_data" =>  json_decode($manuel_data_cart->adults_data ?? '[]') ?? [],
                "children_data" =>  json_decode($manuel_data_cart->children_data ?? '[]') ?? [],
            ];
        }
        elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
            $visa = [
                "country_visa"=> $manuel_data_cart->country ?? null,
                "travel_date"=> $manuel_data_cart->travel_date ??null,
                "appointment_date"=> $manuel_data_cart->appointment_date ?? null,
                "number"=> $manuel_data_cart->number ?? null, 
                "notes"=> $manuel_data_cart->notes ?? null,
                "adults_data" =>  json_decode($manuel_data_cart->adults_data ?? '[]') ?? [],
                "children_data" =>  json_decode($manuel_data_cart->children_data ?? '[]') ?? [],
            ];
        }
        elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
            $flight = [	
                "type" => $manuel_data_cart->type ?? null, 
                "direction" =>  $manuel_data_cart->direction ?? null,
                "from_to" =>  json_decode($manuel_data_cart->from_to ?? '[]') ?? [],
                "departure" =>  $manuel_data_cart->departure ?? null,
                "arrival" => $manuel_data_cart->arrival ?? null,   
                "class" =>  $manuel_data_cart->class ?? null,
                "adults" => $manuel_data_cart->adults ?? null, 
                "childreen" => $manuel_data_cart->childreen ?? null, 
                "infants" =>  $manuel_data_cart->infants ?? null,
                "airline" =>  $manuel_data_cart->airline ?? null,
                "ticket_number" =>  $manuel_data_cart->ticket_number ?? null,
                "adult_price" => $manuel_data_cart->adult_price ?? null, 
                "child_price" => $manuel_data_cart->child_price ?? null, 
                "ref_pnr" =>  $manuel_data_cart->ref_pnr ?? null,
                "adults_data" =>  json_decode($manuel_data_cart->adults_data ?? '[]') ?? [],
                "children_data" =>  json_decode($manuel_data_cart->children_data ?? '[]') ?? [],
            ];
        }
        elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
            $tour = [
                "tour" => $manuel_data_cart->tour ?? null, 
                "type" => $manuel_data_cart->type ?? null, 
                "adult_price" => $manuel_data_cart->adult_price ?? null, 
                "child_price" => $manuel_data_cart->child_price ?? null, 
                "adults" => $manuel_data_cart->adults ?? null, 
                "childreen" => $manuel_data_cart->childreen ?? null, 
                "flight_date" => $manuel_data_cart->flight_date ?? null, 
                "tour_buses" => json_decode($manuel_data_cart->tour_buses ?? '[]') ?? [], 
                "tour_hotels" => json_decode($manuel_data_cart->tour_hotels ?? '[]') ?? [], 
                "adults_data" =>  json_decode($manuel_data_cart->adults_data ?? '[]') ?? [],
                "children_data" =>  json_decode($manuel_data_cart->children_data ?? '[]') ?? [],
            ];
        }
        $arr = [
            "from_supplier"=> $this->supplier_agent->where('id', $manuel_data_cart->from_supplier_id)->first()->name ?? null,
            "from_service"=>  $service,
            "mark_up_type"=> $manuel_data_cart->mark_up_type,
            "mark_up"=> $manuel_data_cart->mark_up,
            "price"=> $manuel_data_cart->price,
            "country"=> $this->contries->where('id', $manuel_data_cart->country_id)->first()->name ?? null,
            "city"=> $this->cities->where('id', $manuel_data_cart->city_id ?? 0)->first()->name ?? null,
            "currency"=> $this->currency->where('id', $manuel_data_cart->currency_id ?? 0)->first()->name ?? null,
            "to_client"=> $manuel_data_cart->to_supplier_id ?
            $this->supplier_agent->where('id', $manuel_data_cart->to_supplier_id)->first()->name ?? null
            :$this->customers->where('id', $manuel_data_cart->to_customer_id)->first()->name ?? null,
            "role"=> $manuel_data_cart->to_supplier_id ? 'supplier' : 'customer',
            "taxes"=> json_decode($manuel_data_cart->taxes ?? '[]') ?? [],
            "tax_type"=> $manuel_data_cart->tax_type,
            "cost"=> $manuel_data_cart->cost,
            "total_price"=> $manuel_data_cart->total_price,
            'hotel' => $hotel,
            'visa' => $visa,
            'flight' => $flight,
            'bus' => $bus,
            'tour' => $tour,
        ];
   
        return response()->json([
            'data' => $arr,
        ]);
    }

    public function manuel_bookings(){
        // https://travelta.online/agent/manual_booking/items
        $manuel_data_cart = $this->manuel_data_cart
        ->get();
        $data = [];
        foreach ($manuel_data_cart as $item) {
            $manuel_item = json_decode($item->cart);
            if (!empty($manuel_item->to_supplier_id)) {
                $to_client = $this->supplier_agent
                ->where('id', $manuel_item->to_supplier_id)
                ->first();
            } else {   
                $to_client = $this->customers
                ->where('id', $manuel_item->to_customer_id)
                ->first();
            }

            $service = $this->services->where('id', $manuel_item->from_service_id)->first()->service_name ?? null;
            $hotel = null;
            $bus = null;
            $visa = null;
            $flight = null;
            $tour = null;
            if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
                $hotel = [
                    "check_in" => $manuel_item->check_in ?? null,
                    "check_out" => $manuel_item->check_out ?? null,
                    "hotel_name" => $manuel_item->hotel_name ?? null, 
                    "nights" => $manuel_item->nights ?? null,
                    "room_type" => $manuel_item->room_type ?? null,
                    "room_quantity" => $manuel_item->room_quantity ?? null,
                    "childreen" =>  $manuel_item->childreen ?? null,
                    "adults" =>  $manuel_item->adults ?? null,
                    "adults_data" =>  json_decode($manuel_item->adults_data ?? '[]') ?? [],
                    "children_data" =>  json_decode($manuel_item->children_data ?? '[]') ?? [],
                ];
            }
            elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
                $bus = [
                    "from" => $manuel_item->from ?? null,
                    "to" => $manuel_item->to ?? null,  
                    "bus" => $manuel_item->bus ?? null,
                    "departure" =>  $manuel_item->departure ?? null,
                    "arrival" => $manuel_item->arrival ?? null,  
                    "adults" => $manuel_item->adults ?? null, 
                    "childreen" => $manuel_item->childreen ?? null, 
                    "adult_price" => $manuel_item->adult_price ?? null, 
                    "child_price" => $manuel_item->child_price ?? null, 
                    "bus" => $manuel_item->bus ?? null,
                    "bus_number" => $manuel_item->bus_number ?? null,
                    "driver_phone" => $manuel_item->driver_phone ?? null, 
                    "adults_data" =>  json_decode($manuel_item->adults_data ?? '[]') ?? [],
                    "children_data" =>  json_decode($manuel_item->children_data ?? '[]') ?? [],
                ];
            }
            elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
                $visa = [
                    "country_visa"=> $manuel_item->country ?? null,
                    "travel_date"=> $manuel_item->travel_date ??null,
                    "appointment_date"=> $manuel_item->appointment_date ?? null,
                    "number"=> $manuel_item->number ?? null, 
                    "notes"=> $manuel_item->notes ?? null,
                    "adults_data" =>  json_decode($manuel_item->adults_data ?? '[]') ?? [],
                    "children_data" =>  json_decode($manuel_item->children_data ?? '[]') ?? [],
                ];
            }
            elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
                $flight = [	
                    "type" => $manuel_item->type ?? null, 
                    "direction" =>  $manuel_item->direction ?? null,
                    "from_to" =>  json_decode($manuel_item->from_to ?? '[]') ?? [],
                    "departure" =>  $manuel_item->departure ?? null,
                    "arrival" => $manuel_item->arrival ?? null,   
                    "class" =>  $manuel_item->class ?? null,
                    "adults" => $manuel_item->adults ?? null, 
                    "childreen" => $manuel_item->childreen ?? null, 
                    "infants" =>  $manuel_item->infants ?? null,
                    "airline" =>  $manuel_item->airline ?? null,
                    "ticket_number" =>  $manuel_item->ticket_number ?? null,
                    "adult_price" => $manuel_item->adult_price ?? null, 
                    "child_price" => $manuel_item->child_price ?? null, 
                    "ref_pnr" =>  $manuel_item->ref_pnr ?? null,
                    "adults_data" =>  json_decode($manuel_item->adults_data ?? '[]') ?? [],
                    "children_data" =>  json_decode($manuel_item->children_data ?? '[]') ?? [],
                ];
            }
            elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
                $tour = [
                    "tour" => $manuel_item->tour ?? null, 
                    "type" => $manuel_item->type ?? null, 
                    "adult_price" => $manuel_item->adult_price ?? null, 
                    "child_price" => $manuel_item->child_price ?? null, 
                    "adults" => $manuel_item->adults ?? null, 
                    "childreen" => $manuel_item->childreen ?? null, 
                    "flight_date" => $manuel_item->flight_date ?? null, 
                    "tour_buses" => json_decode($manuel_item->tour_buses ?? '[]') ?? [], 
                    "tour_hotels" => json_decode($manuel_item->tour_hotels ?? '[]') ?? [], 
                    "adults_data" =>  json_decode($manuel_item->adults_data ?? '[]') ?? [],
                    "children_data" =>  json_decode($manuel_item->children_data ?? '[]') ?? [],
                ];
            }
            $arr = [
                'id' => $item->id,
                "from_supplier"=> $this->supplier_agent->where('id', $manuel_item->from_supplier_id)->first()->name ?? null,
                "from_service"=>  $service,
                "mark_up_type"=> $manuel_item->mark_up_type,
                "mark_up"=> $manuel_item->mark_up,
                "price"=> $manuel_item->price,
                "country"=> $this->contries->where('id', $manuel_item->country_id)->first()->name ?? null,
                "city"=> $this->cities->where('id', $manuel_item->city_id ?? 0)->first()->name ?? null,
                "currency"=> $this->currency->where('id', $manuel_item->currency_id ?? 0)->first()->name ?? null,
                "to_client"=> $manuel_item->to_supplier_id ?
                $this->supplier_agent->where('id', $manuel_item->to_supplier_id)->first()->name ?? null
                :$this->customers->where('id', $manuel_item->to_customer_id)->first()->name ?? null,
                "role"=> $manuel_item->to_supplier_id ? 'supplier' : 'customer',
                "taxes"=> json_decode($manuel_item->taxes ?? '[]') ?? [],
                "tax_type"=> $manuel_item->tax_type,
                "cost"=> $manuel_item->cost,
                "total_price"=> $manuel_item->total_price,
                'hotel' => $hotel,
                'visa' => $visa,
                'flight' => $flight,
                'bus' => $bus,
                'tour' => $tour,
            ];
            $data[] = $arr;
        }
   
        return response()->json([
            'data' => $data,
        ]);
    }

    public function delete_cart($id){
        // https://travelta.online/agent/manual_booking/cart/delete/{id}
        $manuel_data_cart = $this->manuel_data_cart
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }

    public function booking(CartBookingRequest $request){
        // Hotel => "success": {"to_customer_id": "1", to_supplier_id": "1","from_supplier_id": "2","from_service_id": "1","cost": "100","price": "200","currency_id": "1","tax_type": "include", "taxes":"[1,2]","total_price": "400","country_id": "1","city_id": "1","mark_up": "100","mark_up_type": "value","to_customer_id": "4","check_in": "2024-05-05","check_out": "2024-07-07","nights": "3","hotel_name": "Hilton","room_type": "2","room_quantity": "10","adults": "25","childreen": "10"}
        // Bus => "success": {"to_customer_id": "1", to_supplier_id": "1","from_supplier_id": "2","from_service_id": "1","cost": "100","price": "200","currency_id": "1","tax_type": "include", "taxes":"[1,2]","total_price": "400","country_id": "1","city_id": "1","mark_up": "100","mark_up_type": "value","to_customer_id": "4","from": "Alex","to": "Sharm","departure": "2024-05-05 11:30:00","arrival": "2024-07-07 11:30:00","adults": "2","childreen": "10","adult_price": "250","child_price": "100","bus": "Travelta","bus_number": "12345","driver_phone": "01234566"}
        // Visa => "success": {"to_customer_id": "1", to_supplier_id": "1","from_supplier_id": "2","from_service_id": "1","cost": "100","price": "200","currency_id": "1","tax_type": "include", "taxes":"[1,2]","total_price": "400","country_id": "1","city_id": "1","mark_up": "100","mark_up_type": "value","to_customer_id": "4","country": "Alex","travel_date": "2024-12-30","appointment_date": "2024-12-28","number": "10","customers": "['Ahmed', 'Mohamed']","notes": "Hello"}
        // Flight => "success": {"to_customer_id": "1", to_supplier_id": "1","from_supplier_id": "2","from_service_id": "1","cost": "100","price": "200","currency_id": "1","tax_type": "include", "taxes":"[1,2]","total_price": "400","country_id": "1","city_id": "1","mark_up": "100","mark_up_type": "value","to_customer_id": "4","type": "international","direction": "multi_city","departure": "2024-12-28 11:30:00","arrival": "2024-12-31 11:30:00","customers": "['Ahmed', 'Mohamed']","childreen": "10","adults": "25","infants": "10","adult_price": "2000","child_price": "1000","from_to": "[{'from':'Alex', 'to':'America'}, {'from':'America', 'to':'Italy'}]","class": "first","airline": "El Nile","ticket_number": "123122345","ref_pnr": "675"}
         // Tour => 
        // tour, type, adult_price, child_price, adults, childreen
        // tour_buses [{transportation, seats}],
        // tour_hotels[{destination, hotel_name, room_type, check_in, check_out, nights}] 
        
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        $manuel_data_cart = $this->manuel_data_cart
        ->where('id', $request->cart_id)
        ->first();
        if (empty($manuel_data_cart)) {
            return response()->json(['errors' => 'id is wrong'], 400);
        }
        $manuel_data_cart = json_decode($manuel_data_cart->cart);
        $manuel_data_cart = collect($manuel_data_cart);
        $manuelRequest = $manuel_data_cart->only($this->manuelRequest);
        
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $code = 'm' . rand(10000, 99999) . strtolower(Str::random(1));
        $manuel_booking_data = $this->manuel_booking
        ->where('code', $code)
        ->first();
        while (!empty($manuel_booking_data)) {
            $code = 'm' . rand(10000, 99999) . strtolower(Str::random(1));
            $manuel_booking_data = $this->manuel_booking
            ->where('code', $code)
            ->first();
        }
        $manuel_booking = $this->manuel_booking
        ->create([
            'from_supplier_id' => $manuelRequest['from_supplier_id'] ?? null,
            'from_service_id' => $manuelRequest['from_service_id'] ?? null,
            'mark_up_type' => $manuelRequest['mark_up_type'] ?? null,
            'mark_up' => $manuelRequest['mark_up'] ?? null,
            'price' => $manuelRequest['price'] ?? null,
            'country_id' => $manuelRequest['country_id'] ?? null,
            'city_id' => $manuelRequest['city_id'] ?? null,
            'tax_type' => $manuelRequest['tax_type'] ?? null,
            'cost' => $manuelRequest['cost'] ?? null,
            'total_price' => $manuelRequest['total_price'] ?? null,
            'currency_id' => $manuelRequest['currency_id'] ?? null,
            'to_supplier_id' => $manuelRequest['to_supplier_id'] ?? null,
            $role => $agent_id,
            'code' => $code,
            'payment_type' => $request->payment_type,
        ]);
        try{
            if (isset($request->adults_data) && !empty($request->adults_data)) {
                $adults_data = json_decode($request->adults_data) ?? [];
                foreach ($adults_data as $item) {	
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'title' => $item['title'] ?? null,
                        'first_name' => $item['first_name'] ?? null,
                        'last_name' => $item['last_name'] ?? null,
                    ]);
                }
                $child_data = json_decode($request->child_data) ?? [];
                foreach ($child_data as $item) {	
                    $this->child
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'age' => $item['age'] ?? null,
                        'first_name' => $item['first_name'] ?? null,
                        'last_name' => $item['last_name'] ?? null,
                    ]);
                }
            }
            $taxes = is_string($manuel_data_cart['taxes']) ? json_decode($manuel_data_cart['taxes']) : $manuel_data_cart['taxes'];
            $manuel_booking->taxes()->attach($taxes);
            $service = $this->services
            ->where('id', $manuel_data_cart['from_service_id'])
            ->first()->service_name;
            if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
                $hotelRequest = [
                    'check_in' => $manuel_data_cart['check_in'] ?? null,
                    'check_out' => $manuel_data_cart['check_out'] ?? null,
                    'nights' => $manuel_data_cart['nights'] ?? null,
                    'hotel_name' => $manuel_data_cart['hotel_name'] ?? null,
                    'room_type' => $manuel_data_cart['room_type'] ?? null,
                    'room_quantity' => $manuel_data_cart['room_quantity'] ?? null,
                    'adults' => $manuel_data_cart['adults'] ?? null,
                    'childreen' => $manuel_data_cart['childreen'] ?? null,
                ];
                $hotelRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_hotel = $this->manuel_hotel
                ->create($hotelRequest);
            }
            elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
                $busRequest = [ 
                    'from' => $manuel_data_cart['from'] ?? null,
                    'to' => $manuel_data_cart['to'] ?? null,
                    'departure' => $manuel_data_cart['departure'] ?? null,
                    'arrival' => $manuel_data_cart['arrival'] ?? null,
                    'adults' => $manuel_data_cart['adults'] ?? null,
                    'childreen' => $manuel_data_cart['childreen'] ?? null,
                    'adult_price' => $manuel_data_cart['adult_price'] ?? null,
                    'child_price' => $manuel_data_cart['child_price'] ?? null,
                    'bus' => $manuel_data_cart['bus'] ?? null,
                    'bus_number' => $manuel_data_cart['bus_number'] ?? null,
                    'driver_phone' => $manuel_data_cart['driver_phone'] ?? null,
                ];
                $busRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_bus = $this->manuel_bus
                ->create($busRequest);
            }
            elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
                $visaRequest = [
                    'country' => $manuel_data_cart['country'] ?? null,
                    'travel_date' => $manuel_data_cart['travel_date'] ?? null,
                    'appointment_date' => $manuel_data_cart['appointment_date'] ?? null,
                    'notes' => $manuel_data_cart['notes'] ?? null,
                    'number' => $manuel_data_cart['number'] ?? null,
                ];
                $visaRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_visa = $this->manuel_visa
                ->create($visaRequest);
            }
            elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
                $flightRequest = [
                    'type' => $manuel_data_cart['type'] ?? null,
                    'direction' => $manuel_data_cart['direction'] ?? null,
                    'from_to' => $manuel_data_cart['from_to'] ?? null,
                    'departure' => $manuel_data_cart['departure'] ?? null,
                    'arrival' => $manuel_data_cart['arrival'] ?? null,
                    'class' => $manuel_data_cart['class'] ?? null,
                    'adults' => $manuel_data_cart['adults'] ?? null,
                    'childreen' => $manuel_data_cart['childreen'] ?? null,
                    'infants' => $manuel_data_cart['infants'] ?? null,
                    'airline' => $manuel_data_cart['airline'] ?? null,
                    'ticket_number' => $manuel_data_cart['ticket_number'] ?? null,
                    'adult_price' => $manuel_data_cart['adult_price'] ?? null,
                    'child_price' => $manuel_data_cart['child_price'] ?? null,
                    'ref_pnr' => $manuel_data_cart['ref_pnr'] ?? null,
                ];
                $flightRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_flight = $this->manuel_flight
                ->create($flightRequest);
            }
            elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
                $tourRequest = [
                    'tour' => $manuel_data_cart['tour'] ?? null,
                    'type' => $manuel_data_cart['type'] ?? null,
                    'adult_price' => $manuel_data_cart['adult_price'] ?? null,
                    'child_price' => $manuel_data_cart['child_price'] ?? null,
                    'adults' => $manuel_data_cart['adults'] ?? null,
                    'childreen' => $manuel_data_cart['childreen'] ?? null,
                ];
                if(isset($manuel_data_cart['flight_date']) && !empty($manuel_data_cart['flight_date'])){
                    $tourRequest['flight_date'] = $manuel_data_cart['flight_date'];
                }
                $tourRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_tour = $this->manuel_tour
                ->create($tourRequest);
                $manuel_tour_bus = is_string($manuel_data_cart['tour_buses']) ? json_decode($manuel_data_cart['tour_buses']) : $manuel_data_cart['taxes'];
                $manuel_tour_hotel = is_string($manuel_data_cart['tour_hotels']) ? json_decode($manuel_data_cart['tour_hotels']) : $manuel_data_cart['taxes']; 
            // return $manuel_tour_bus;
            if ($manuel_tour_bus) {
                    foreach ($manuel_tour_bus as $item) {
                        $this->manuel_tour_bus
                        ->create([
                            'transportation' => $item->transportation,
                            'manuel_tour_id' => $manuel_tour->id,
                            'seats' => $item->seats,
                        ]);
                    }
            }
            if ($manuel_tour_hotel) {
                    foreach ($manuel_tour_hotel as $item) {
                        $this->manuel_tour_hotel
                        ->create([
                            'destination' => $item->destination,
                            'manuel_tour_id' => $manuel_tour->id,
                            'hotel_name' => $item->hotel_name,
                            'room_type' => $item->room_type,
                            'check_in' => $item->check_in,
                            'check_out' => $item->check_out,
                            'nights' => $item->nights,
                        ]);
                    }
            }
            }
            if (isset($manuel_data_cart['adults_data']) && !empty($manuel_data_cart['adults_data'])) {
                $adults = is_string($manuel_data_cart['adults_data']) ?json_decode($manuel_data_cart['adults_data']) :$manuel_data_cart['adults_data'];
                foreach ($adults as $item) {
                    $this->adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                    ]);
                }
            }
            if (isset($manuel_data_cart['child_data']) && !empty($manuel_data_cart['child_data'])) {
                $child = is_string($manuel_data_cart['child_data']) ?json_decode($manuel_data_cart['child_data']) :$manuel_data_cart['child_data'];
                foreach ($child as $item) {
                    $this->child
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                    ]);
                }
            }
            // Cart
            // payment_type, total_cart
            // payment_methods[amount, payment_method_id, image]
            // payments [{amount, date}]
            if ($request->payment_methods) {
                $payment_methods = is_string($request->payment_methods) ? 
                json_decode($request->payment_methods) : $request->payment_methods;
                foreach ($payment_methods as $item) {
                    $cartRequest = [
                        'manuel_booking_id' => $manuel_booking->id,
                        'total' => $request->total_cart,
                        'payment' => $item->amount ?? 0,
                        'payment_method_id' => $item->payment_method_id,
                    ];
                    if ($item->image && !empty($item->image)) {
                        $image_path = $this->storeBase64Image($item->image);
                        $cartRequest['image'] = $image_path;
                    }
                    $manuel_cart = $this->manuel_cart
                    ->create($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id)
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + $item->amount;
                }
            }
            if ($request->payment_type == 'partial' || $request->payment_type == 'later') {
                $validation = Validator::make($request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($request->payments) ? json_decode($request->payments)
                : $request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount,
                        'date' => $item->date,
                    ]);
                }
            }
             $this->manuel_data_cart
            ->where('id', $request->cart_id)
            ->delete();

            return response()->json([
                'success' => $request->all(),
            ]);
        } catch (\Throwable $th) {
            $manuel_booking->delete();
            return response()->json([
                'faild' => 'something wrong',
            ], 400);
        }
    }
}
