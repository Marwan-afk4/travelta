<?php

namespace App\Http\Controllers\Api\Agent\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\booking_request\BookingRequestRequest;

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

        $adult_data = is_string($request->adult_data) ? json_decode($request->adult_data):$request->adult_data;
        $child_data = is_string($request->child_data) ? json_decode($request->child_data):$request->child_data;
        foreach ($adult_data as $item) {
            $this->request_adults
            ->create([
                'title' => $item['title'],
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'], 
                'request_booking_id' => $request_booking->id
            ]);
        }
        foreach ($child_data as $item) {
            $this->request_children
            ->create([
                'age' => $item['age'],
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'], 
                'request_booking_id' => $request_booking->id
            ]);
        }
        $hotelRequest['request_booking_id'] = $request_booking->id;
        $this->request_hotel
        ->create($hotelRequest);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
