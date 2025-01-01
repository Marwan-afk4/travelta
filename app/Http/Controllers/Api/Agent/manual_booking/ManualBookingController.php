<?php

namespace App\Http\Controllers\Api\Agent\manual_booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\manuel_booking\ManuelBookingRequest;

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

class ManualBookingController extends Controller
{
    public function __construct(private City $cities, private Country $contries,
    private CustomerData $customer_data, private SupplierAgent $supplier_agent,
    private Service $services, private Tax $taxes, private ManuelBooking $manuel_booking,
    private ManuelBus $manuel_bus, private ManuelFlight $manuel_flight, 
    private ManuelHotel $manuel_hotel, private ManuelTour $manuel_tour, 
    private ManuelVisa $manuel_visa, private ManuelTourBus $manuel_tour_bus,
    private ManuelTourHotel $manuel_tour_hotel, private CurrencyAgent $currency,
    private Adult $adults, private Child $child){}
    
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
            'currencies' => $currencies
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

    public function booking(ManuelBookingRequest $request){
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
        $manuelRequest = $request->validated();

        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $manuelRequest['affilate_id'] = $agent_id;
        }
        else{
            $manuelRequest['agent_id'] = $agent_id;
        }
        $manuel_booking = $this->manuel_booking
        ->create($manuelRequest);
        try {
            $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
            $manuel_booking->taxes()->attach($taxes);
            $service = $this->services
            ->where('id', $request->from_service_id)
            ->first()->service_name->hdfgh;
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
                $hotelRequest = $request->only($this->hotelRequest);
                $hotelRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_hotel = $this->manuel_hotel
                ->create($hotelRequest);
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
                $busRequest = $request->only($this->busRequest);
                $busRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_bus = $this->manuel_bus
                ->create($busRequest);
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
                $visaRequest = $request->only($this->visaRequest);
                $visaRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_visa = $this->manuel_visa
                ->create($visaRequest);
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
                $flightRequest = $request->only($this->flightRequest);
                $flightRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_flight = $this->manuel_flight
                ->create($flightRequest);
            }
            elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
                $validation = Validator::make($request->all(), [
                    'tour' => 'required',
                    'type' => 'required|in:domestic,international',
                    'adult_price' => 'numeric',
                    'child_price' => 'numeric',
                    'adults' => 'numeric',
                    'childreen' => 'numeric', 
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $tourRequest = $request->only($this->tourRequest);
                $tourRequest['manuel_booking_id'] = $manuel_booking->id;
                $manuel_tour = $this->manuel_tour
                ->create($tourRequest);
                $manuel_tour_bus = is_string($request->tour_buses) ? json_decode($request->tour_buses) : $request->taxes;
                $manuel_tour_hotel = is_string($request->tour_hotels) ? json_decode($request->tour_hotels) : $request->taxes; 
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
            if (isset($request->adults_data) && !empty($request->adults_data)) {
                $adults = is_string($request->adults_data) ?json_decode($request->adults_data) :$request->adults_data;
                foreach ($adults as $item) {
                    $this->adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                    ]);
                }
            }
            if (isset($request->child_data) && !empty($request->child_data)) {
                $child = is_string($request->child_data) ?json_decode($request->child_data) :$request->child_data;
                foreach ($child as $item) {
                    $this->child
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                    ]);
                }
            } 
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
