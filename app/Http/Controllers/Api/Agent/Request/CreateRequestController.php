<?php

namespace App\Http\Controllers\Api\Agent\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\booking_request\BookingRequestRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\RequestBooking;
use App\Models\RequestAdult;
use App\Models\RequestChild;
use App\Models\RequestHotel;
use App\Models\RequestBus;
use App\Models\RequestFlight;
use App\Models\RequestTour;
use App\Models\RequestVisa;
use App\Models\RequestTourBus;
use App\Models\RequestTourHotel;

class CreateRequestController extends Controller
{
    public function __construct(
    private RequestBooking $request_booking,
    private RequestAdult $request_adults,
    private RequestChild $request_children,
    private RequestHotel $request_hotel,
    private RequestBus $request_bus,
    private RequestFlight $request_flight,
    private RequestTour $request_tour,
    private RequestVisa $request_visa,
    private RequestTourBus $request_tour_bus,
    private RequestTourHotel $request_tour_hotel){}

    protected $requestBookingRequest = [
        'customer_id',
        'admin_agent_id',
        'service_id',
        'currency_id', 
        'expected_revenue',
        'priority',
        'stages',
    ];
    protected $adultRequest = [
        'title',
        'first_name',
        'last_name',
        // 'request_booking_id',
    ];
    protected $childRequest = [
        'age',
        'first_name',
        'last_name',
        // 'request_booking_id',
    ];
    protected $hotelRequest = [
        'check_in',
        'check_out', 
        'nights',
        'hotel_name',
        'room_type',
        'room_quantity',
        'adults',
        'childreen',
        'notes',
        // 'request_booking_id',
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
        'driver_phone',
        'notes',
        // 'request_booking_id',
    ];
    protected $visaRequest = [
        'country',
        'travel_date',
        'appointment_date',
        'notes',
        'adults',
        'childreen',
        // 'request_booking_id',
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
        'notes',
        // 'request_booking_id',
    ];
    protected $tourRequest = [
        'tour',
        'type', 
        'flight_date',
        'adult_price',
        'child_price',
        'adults',
        'childreen',
        'notes',
        // 'request_booking_id',
    ];

    public function add_hotel(BookingRequestRequest $request){
        // agent/request/add_hotel
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, stages, 
        // check_in, check_out,  nights, hotel_name, room_type, room_quantity, adults, 
        // childreen, notes,
        // adult_data => [{title, first_name, last_name}]
        // child_data => [{age, first_name, last_name}]
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
        $requestBookingRequest = $request->only($this->requestBookingRequest);
        $hotelRequest = $request->only($this->hotelRequest);
        $requestBookingRequest[$role] = $agent_id;
        $request_booking = $this->request_booking
        ->create($requestBookingRequest);

        try {
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            foreach ($adult_data as $item) {
                $this->request_adults
                ->create([
                    'title' => $item->title,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            foreach ($child_data as $item) {
                $this->request_children
                ->create([
                    'age' => $item->age,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            $hotelRequest['request_booking_id'] = $request_booking->id;
            $this->request_hotel
            ->create($hotelRequest);

            return response()->json([
                'success' => 'You add data success'
            ]); 
        } catch (\Throwable $th) {
            $request_booking->delete();
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function add_bus(BookingRequestRequest $request){
        // agent/request/add_bus
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, stages, 
        // from, to, departure, arrival, adults, childreen, 
        // adult_price, child_price, bus, bus_number, driver_phone, notes, 
        // adult_data => [{title, first_name, last_name}]
        // child_data => [{age, first_name, last_name}]
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
        $requestBookingRequest = $request->only($this->requestBookingRequest);
        $busRequest = $request->only($this->busRequest);
        $requestBookingRequest[$role] = $agent_id;
        $request_booking = $this->request_booking
        ->create($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            foreach ($adult_data as $item) {
                $this->request_adults
                ->create([
                    'title' => $item->title,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            foreach ($child_data as $item) {
                $this->request_children
                ->create([
                    'age' => $item->age,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            $busRequest['request_booking_id'] = $request_booking->id;
            $this->request_bus
            ->create($busRequest);

            return response()->json([
                'success' => 'You add data success'
            ]);
        } catch (\Throwable $th) {
            $request_booking->delete();
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function add_visa(BookingRequestRequest $request){
        // agent/request/add_visa
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, stages, 
        // country,travel_date,appointment_date,notes, adults,childreen, 
        // adult_data => [{title, first_name, last_name}]
        // child_data => [{age, first_name, last_name}]
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
        $requestBookingRequest = $request->only($this->requestBookingRequest);
        $visaRequest = $request->only($this->visaRequest);
        $requestBookingRequest[$role] = $agent_id;
        $request_booking = $this->request_booking
        ->create($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            foreach ($adult_data as $item) {
                $this->request_adults
                ->create([
                    'title' => $item->title,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            foreach ($child_data as $item) {
                $this->request_children
                ->create([
                    'age' => $item->age,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            $visaRequest['request_booking_id'] = $request_booking->id;
            $this->request_visa
            ->create($visaRequest);

            return response()->json([
                'success' => 'You add data success'
            ]);
        } catch (\Throwable $th) {
            $request_booking->delete();
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function add_flight(BookingRequestRequest $request){
        // agent/request/add_flight
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, stages, 
        // 'type' => [domestic, international], 'direction' => [one_way, round_trip, multi_city], 'from_to' => [{'from':'Alex', 'to':'America'}, {'from':'America', 'to':'Italy'}], 'departure', 'arrival', 'class', 'adults', 
        // 'childreen', 'infants', 'airline', 'ticket_number', 'adult_price', 
        // 'child_price', 'ref_pnr',  'notes',
        // adult_data => [{title, first_name, last_name}]
        // child_data => [{age, first_name, last_name}]
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
        $requestBookingRequest = $request->only($this->requestBookingRequest);
        $flightRequest = $request->only($this->flightRequest);
        $requestBookingRequest[$role] = $agent_id;
        $request_booking = $this->request_booking
        ->create($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            foreach ($adult_data as $item) {
                $this->request_adults
                ->create([
                    'title' => $item->title,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            foreach ($child_data as $item) {
                $this->request_children
                ->create([
                    'age' => $item->age,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            $flightRequest['request_booking_id'] = $request_booking->id;
            $this->request_flight
            ->create($flightRequest);

            return response()->json([
                'success' => 'You add data success'
            ]);
        } catch (\Throwable $th) {
            $request_booking->delete();
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function add_tour(BookingRequestRequest $request){
        // agent/request/add_tour
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, stages, 
        // tour, type => [domestic, international],  flight_date, adult_price, child_price, 
        // adults, childreen, notes
        // tour_bus [transportation, seats, departure => if flight]
        // tour_hotels [destination, hotel_name, room_type, check_in, check_out, nights]
        // adult_data => [{title, first_name, last_name}]
        // child_data => [{age, first_name, last_name}]
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
        $requestBookingRequest = $request->only($this->requestBookingRequest);
        $tourRequest = $request->only($this->tourRequest);
        $requestBookingRequest[$role] = $agent_id;
        $request_booking = $this->request_booking
        ->create($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            foreach ($adult_data as $item) {
                $this->request_adults
                ->create([
                    'title' => $item->title,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            foreach ($child_data as $item) {
                $this->request_children
                ->create([
                    'age' => $item->age,
                    'first_name' => $item->first_name,
                    'last_name' => $item->last_name, 
                    'request_booking_id' => $request_booking->id
                ]);
            }
            $tourRequest['request_booking_id'] = $request_booking->id;
            $request_tour = $this->request_tour
            ->create($tourRequest);
            $tour_bus = is_string($request->tour_bus) ? json_decode($request->tour_bus): $request->tour_bus;
            $tour_hotels = is_string($request->tour_hotels) ? json_decode($request->tour_hotels): $request->tour_hotels;
            foreach ($tour_bus as $item) {
                $this->request_tour_bus
                ->create([
                    'request_tour_id' => $request_tour->id,
                    'transportation' => $item->transportation,
                    'seats' => $item->seats,
                    'departure' => $item->departure ?? null,
                ]);
            }
            foreach ($tour_hotels as $item) {
                $this->request_tour_hotel
                ->create([
                    'request_tour_id' => $request_tour->id,
                    'destination' => $item->destination,
                    'hotel_name' => $item->hotel_name,
                    'room_type' => $item->room_type,
                    'check_in' => $item->check_in,
                    'check_out' => $item->check_out,
                    'nights' => $item->nights,
                ]);
            }

            return response()->json([
                'success' => 'You add data success'
            ]);
        } catch (\Throwable $th) {
            $request_booking->delete();
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function priority(Request $request, $id){
        // /agent/request/priority/{id}
        $validation = Validator::make($request->all(), [
            'priority' => 'required|in:Low,Normal,High',
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
        $request_booking = $this->request_booking
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'priority' => $request->priority
        ]);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function stages(Request $request, $id){
        // /agent/request/stages/{id}
        $validation = Validator::make($request->all(), [
            'stages' => 'required|in:Pending,Price quotation,Negotiation,Won,Won Canceled,Lost',
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
        $request_booking = $this->request_booking
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'stages' => $request->stages
        ]);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }
}
