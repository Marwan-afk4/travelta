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

class ManualBookingController extends Controller
{
    public function __construct(private City $cities, private Country $contries,
    private CustomerData $customer_data, private SupplierAgent $supplier_agent,
    private Service $services, private Tax $taxes, private ManuelBooking $manuel_booking,
    private ManuelBus $manuel_bus, private ManuelFlight $manuel_flight, 
    private ManuelHotel $manuel_hotel, private ManuelTour $manuel_tour, 
    private ManuelVisa $manuel_visa, private ManuelTourBus $manuel_tour_bus,
    private ManuelTourHotel $manuel_tour_hotel){}
    
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

    public function lists(){
        // https://travelta.online/agent/manual_booking/lists
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
        $manuelRequest = $request->validated();
        $manuel_booking = $this->manuel_booking
        ->create($manuelRequest);
        $service = $this->services
        ->where('id', $request->from_service_id)
        ->first()->service_name;
        if ($service == 'hotel' || $service == 'Hotel') {
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
            $manuel_tour_bus = is_array($request->tour_buses) ?? json_decode($request->tour_buses);
            $manuel_tour_hotel = is_array($request->tour_hotels) ?? json_decode($request->tour_hotels);
            foreach ($manuel_tour_bus as $item) {
                $this->manuel_tour_bus
                ->create([
                    'transportation' => $item['transportation'],
                    'manuel_tour_id' => $manuel_tour->id,
                    'seats' => $item['seats'],
                ]);
            }
            foreach ($manuel_tour_hotel as $item) {
                $this->manuel_tour_hotel
                ->create([
                    'destination' => $item['destination'],
                    'manuel_tour_id' => $manuel_tour->id,
                    'hotel_name' => $item['hotel_name'],
                    'room_type' => $item['room_type'],
                    'check_in' => $item['check_in'],
                    'check_out' => $item['check_out'],
                    'nights' => $item['nights'],
                ]);
            }
        }

        return response()->json([
            'success' => $request->all(),
        ]);
    }
}
