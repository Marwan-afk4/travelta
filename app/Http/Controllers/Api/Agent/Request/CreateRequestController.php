<?php

namespace App\Http\Controllers\Api\Agent\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\booking_request\BookingRequestRequest;
use Illuminate\Support\Facades\Validator;

use App\Models\RequestBooking;
use App\Models\RequestStage;
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
    private RequestTourHotel $request_tour_hotel,
    private RequestStage $request_stage){}

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
    protected $stageRequest = [
        'action',
        'priority',
        'follow_up_date',
        'result',
    ];

    public function add_hotel(BookingRequestRequest $request){
        // agent/request/add_hotel
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority,
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
        // priority,
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
        // priority, 
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
        // priority,
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
        // priority,
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
    //____________________________________________________________
    public function request_item(Request $request, $id){
        // agent/request/item/{id}
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
        ->with(['customer', 'service', 'admin_agent',
        'currency', 'adults', 'children', 'hotel', 'bus',
        'flight', 'tour' => function($query){
            $query->with(['bus', 'hotel']);
        }, 'visa'])
        ->where('id', $id)
        ->first();

        return response()->json([
            'request_booking' => $request_booking
        ]);
    }
    //____________________________________________________________

    public function update_hotel(BookingRequestRequest $request, $id){
        // agent/request/update_hotel/{id}
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority,
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
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $request_booking->update($requestBookingRequest);
        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            $this->request_adults
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            $this->request_children
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            if ($adult_data) {
                foreach ($adult_data as $item) {
                    $this->request_adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            if ($child_data) {
                foreach ($child_data as $item) {
                    $this->request_children
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            $this->request_hotel
            ->where('request_booking_id', $request_booking->id)
            ->update($hotelRequest);

            return response()->json([
                'success' => 'You update data success'
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function update_bus(BookingRequestRequest $request, $id){
        // agent/request/update_bus/{id}
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority,
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
        ->where($role, $agent_id)
        ->where('id', $id)
        ->first();
        $request_booking->update($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            $this->request_adults
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            $this->request_children
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            if ($adult_data) {
                foreach ($adult_data as $item) {
                    $this->request_adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            if ($child_data) {
                foreach ($child_data as $item) {
                    $this->request_children
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            } 
            $this->request_bus
            ->where('request_booking_id', $request_booking->id)
            ->update($busRequest);

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function update_visa(BookingRequestRequest $request, $id){
        // agent/request/update_visa/{id}
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority, 
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
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $request_booking->update($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            $this->request_adults
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            $this->request_children
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            if ($adult_data) {
                foreach ($adult_data as $item) {
                    $this->request_adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            if ($child_data) {
                foreach ($child_data as $item) {
                    $this->request_children
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            $visaRequest['request_booking_id'] = $request_booking->id;
            $this->request_visa
            ->where('request_booking_id', $request_booking->id)
            ->update($visaRequest);

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function update_flight(BookingRequestRequest $request, $id){
        // agent/request/update_flight/{id}
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority,
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
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $request_booking->update($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            $this->request_adults
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            $this->request_children
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            if ($adult_data) {
                foreach ($adult_data as $item) {
                    $this->request_adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            if ($child_data) {
                foreach ($child_data as $item) {
                    $this->request_children
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            $flightRequest['request_booking_id'] = $request_booking->id;
            $this->request_flight
            ->where('request_booking_id', $request_booking->id)
            ->update($flightRequest);

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }

    public function update_tour(BookingRequestRequest $request, $id){
        // agent/request/update_tour
        // customer_id, admin_agent_id, service_id, currency_id,  expected_revenue, 
        // priority,
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
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $request_booking->update($requestBookingRequest);

        try{
            $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
            $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
            $this->request_adults
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            $this->request_children
            ->where('request_booking_id', $request_booking->id)
            ->delete();
            if ($adult_data) {
                foreach ($adult_data as $item) {
                    $this->request_adults
                    ->create([
                        'title' => $item->title,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            if ($child_data) {
                foreach ($child_data as $item) {
                    $this->request_children
                    ->create([
                        'age' => $item->age,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name, 
                        'request_booking_id' => $request_booking->id
                    ]);
                }
            }
            $request_tour = $this->request_tour
            ->where('request_booking_id', $request_booking->id)
            ->first();
            if (empty($request_tour)) {
                return response()->json([
                    'success' => 'You update data success'
                ]);
            }
            $request_tour->update($tourRequest);
            $tour_bus = is_string($request->tour_bus) ? json_decode($request->tour_bus): $request->tour_bus;
            $tour_hotels = is_string($request->tour_hotels) ? json_decode($request->tour_hotels): $request->tour_hotels;
            
            $this->request_tour_bus
            ->where('request_tour_id', $request_tour->id)
            ->delete();
            $this->request_tour_hotel
            ->where('request_tour_id', $request_tour->id)
            ->delete();
            if ($tour_bus) {
                foreach ($tour_bus as $item) {
                    $this->request_tour_bus
                    ->create([
                        'request_tour_id' => $request_tour->id,
                        'transportation' => $item->transportation,
                        'seats' => $item->seats,
                        'departure' => $item->departure ?? null,
                    ]);
                }
            }
            if ($tour_hotels) {
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
            }

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => 'Something Errors'
            ], 400);
        }
    }
    //_______________________________________________________________________

    public function priority(Request $request, $id){
        // /agent/request/priority/{id}
        // key
        // priority [Low, Normal, High]
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
        // Stages = Pending, Price quotation, Negotiation
        // Keys
        // stages => [Pending,Price quotation,Negotiation,Won,Won Canceled,Lost],
        // action => [call,message,assign_request], follow_up_date, result, 
        // if action = message => key => send_by
        // if action = assign_request => key => admin_agent_id
        // Stages = Won, Lost, Won Canceled
        // Keys
        // stages
        // if stages = Won => key => code
        // if stages = Lost => key => lost_reason
        $validation = Validator::make($request->all(), [
            'stages' => 'required|in:Pending,Price quotation,Negotiation,Won,Won Canceled,Lost',
            'action' => 'in:call,message,assign_request',
            'follow_up_date' => 'date',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->action == 'message') {
            $validation = Validator::make($request->all(), [ 
                'send_by' => 'required',
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            }
        }
        elseif ($request->action == 'assign_request') {
            $validation = Validator::make($request->all(), [ 
                'admin_agent_id' => 'required|exists:admin_agents,id',
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            }
        }
        if ($request->stages == 'Won') {
            $validation = Validator::make($request->all(), [ 
                'code' => 'required|unique:request_bookings,code',
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            }
        }
        if ($request->stages == 'Lost') {
            $validation = Validator::make($request->all(), [ 
                'lost_reason' => 'required',
            ]);
            if($validation->fails()){
                return response()->json(['errors'=>$validation->errors()], 401);
            }
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
        $stageRequest = $request->only($this->stageRequest);
        $stageRequest['request_booking_id'] = $id;
        if ($request->action && $request->action == 'message') { 
            $stageRequest['send_by'] = $request->send_by;
        }
        elseif ($request->action && $request->action == 'assign_request') { 
            $request_booking = $this->request_booking
            ->where('id', $id)
            ->where($role, $agent_id)
            ->update([
                'admin_agent_id' => $request->admin_agent_id
            ]);
        }
        if ($request->stages == 'Won') { 
            $request_booking = $this->request_booking
            ->where('id', $id)
            ->where($role, $agent_id)
            ->update([
                'code' => $request->code
            ]);
        }
        if ($request->stages == 'Lost') { 
            $request_booking = $this->request_booking
            ->where('id', $id)
            ->where($role, $agent_id)
            ->update([
                'lost_reason' => $request->lost_reason
            ]);
        }
        if ($request->action && $request->stages != 'Lost' && $request->stages != 'Won' &&  $request->stages != 'Won Canceled') {
            $this->request_stage
            ->create($stageRequest);
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

    public function notes(Request $request, $id){
        // /agent/request/notes/{id}
        $validation = Validator::make($request->all(), [
            'notes' => 'required',
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
        ->first();

        if ($request_booking->hotel) {
            $request_booking
            ->hotel
            ->update([
                'notes' => $request->notes
            ]);
        }
        elseif ($request_booking->bus) {
            $request_booking
            ->bus
            ->update([
                'notes' => $request->notes
            ]);
        }
        elseif ($request_booking->flight) {
            $request_booking
            ->flight
            ->update([
                'notes' => $request->notes
            ]);
        }
        elseif ($request_booking->tour) {
            $request_booking
            ->tour
            ->update([
                'notes' => $request->notes
            ]);
        }
        elseif ($request_booking->visa) {
            $request_booking
            ->visa
            ->update([
                'notes' => $request->notes
            ]);
        }

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        // /agent/request/delete/{id}
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
        ->delete();

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
