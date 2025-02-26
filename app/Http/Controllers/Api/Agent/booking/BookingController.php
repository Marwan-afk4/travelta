<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBusResource;
use App\Http\Resources\ManuelFlightResource;
use App\Http\Resources\ManuelHotelResource;
use App\Http\Resources\ManuelTourResource;
use App\Http\Resources\ManuelVisaResource;
use App\Http\Resources\EngineHotelResource;
use Illuminate\Support\Facades\Validator;

use App\Models\BookingengineList;
use App\Models\BookingTask;
use App\Models\ManuelBooking;
use App\Models\AffilateAgent;
use App\Models\Service;
use App\Models\Agent;

class BookingController extends Controller
{
    public function __construct(private Service $services, 
    private ManuelBooking $manuel_booking, private AffilateAgent $affilate,
    private Agent $agent, private BookingengineList $booking_engine,
    private BookingTask $booking_task){}

    public function services(){
        $services = $this->services
        ->get();

        return response()->json([
            'services' => $services
        ]);
    }

    public function booking(Request $request){
        // https://travelta.online/agent/booking
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
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }

        $hotel_upcoming = $this->manuel_booking
        ->with(['hotel', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->where('check_in', '>', date('Y-m-d'));
        })
        ->get();
        $bus_upcoming = $this->manuel_booking
        ->with(['bus', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        })
        ->get();
        $visa_upcoming = $this->manuel_booking
        ->with(['visa', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->where('travel_date', '>', date('Y-m-d'));
        })
        ->get();
        $flight_upcoming = $this->manuel_booking
        ->with(['flight', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        })
        ->get();
        $tour_upcoming = $this->manuel_booking
        ->with(['tour' => function($query){
            $query->with([
                'hotel', 'bus'
            ]);
        }, 'taxes', 'from_supplier'])
        ->whereHas('tour.hotel')
        ->where($agent_type, $agent_id)
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_in', '<=', date('Y-m-d'));
        })
        ->get();
        $hotel_upcoming = ManuelHotelResource::collection($hotel_upcoming);
        $bus_upcoming = ManuelBusResource::collection($bus_upcoming);
        $visa_upcoming = ManuelVisaResource::collection($visa_upcoming);
        $flight_upcoming = ManuelFlightResource::collection($flight_upcoming);
        $tour_upcoming = ManuelTourResource::collection($tour_upcoming);

 
        $upcoming = [
            'hotels' => $hotel_upcoming,
            'buses' => $bus_upcoming,
            'visas' => $visa_upcoming,
            'flights' => $flight_upcoming,
            'tours' => $tour_upcoming,
        ]; 
        
        $hotel_current = $this->manuel_booking
        ->with(['hotel', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'))
            ->whereDate('check_out', '>=', date('Y-m-d'));
        })
        ->get(); 
        $bus_current = $this->manuel_booking
        ->with(['bus', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'))
            ->whereDate('arrival', '>=', date('Y-m-d'));
        })
        ->get();
        $visa_current = $this->manuel_booking
        ->with(['visa', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->whereDate('travel_date', date('Y-m-d'));
        })
        ->get(); 
        $flight_current = $this->manuel_booking
        ->with(['flight', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'))
            ->whereDate('arrival', '>=', date('Y-m-d'));
        })
        ->get(); 
        $tour_current = $this->manuel_booking
        ->with(['tour' => function($query){
            $query->with([
                'hotel', 'bus'
            ]);
        }, 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'))
            ->whereDate('check_out', '>=', date('Y-m-d'));
        })
        ->get();
        $hotel_current = ManuelHotelResource::collection($hotel_current);
        $bus_current = ManuelBusResource::collection($bus_current);
        $visa_current = ManuelVisaResource::collection($visa_current);
        $flight_current = ManuelFlightResource::collection($flight_current);
        $tour_current = ManuelTourResource::collection($tour_current);

        $current = [
            'hotels' => $hotel_current,
            'buses' => $bus_current,
            'visas' => $visa_current,
            'flights' => $flight_current,
            'tours' => $tour_current,
        ]; 
        $hotel_past = $this->manuel_booking
        ->with([ 'hotel', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->where('check_out', '<', date('Y-m-d'));
        })
        ->get();
        $bus_past = $this->manuel_booking
        ->with(['bus', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->where('arrival', '<', date('Y-m-d'));
        })
        ->get();
        $visa_past = $this->manuel_booking
        ->with(['visa', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->where('travel_date', '<', date('Y-m-d'));
        })
        ->get();
        $flight_past = $this->manuel_booking
        ->with(['flight', 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->where('arrival', '<', date('Y-m-d'));
        })
        ->get();
        $tour_past = $this->manuel_booking
        ->with(['tour' => function($query){
            $query->with([
                'hotel', 'bus'
            ]);
        }, 'taxes', 'from_supplier'])
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel')
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_out', '>=', date('Y-m-d'));
        })
        ->get();
        $hotel_past = ManuelHotelResource::collection($hotel_past);
        $bus_past = ManuelBusResource::collection($bus_past);
        $visa_past = ManuelVisaResource::collection($visa_past);
        $flight_past = ManuelFlightResource::collection($flight_past);
        $tour_past = ManuelTourResource::collection($tour_past);
        $past = [
            'hotels' => $hotel_past,
            'buses' => $bus_past,
            'visas' => $visa_past,
            'flights' => $flight_past,
            'tours' => $tour_past,
        ];

        // Booking engine
        $booking_engine = $this->booking_engine
        ->where($agent_type, $agent_id)
        ->get();
        $engine_hotel = EngineHotelResource::collection($booking_engine);
        $engine_upcoming = $engine_hotel->where('check_in', '>=', date('Y-m-d'));
        $engine_current = $engine_hotel->where('check_in', '=', date('Y-m-d'));
        $engine_past = $engine_hotel->where('check_in', '<=', date('Y-m-d'));

        return response()->json([
            'upcoming' => $upcoming,
            'current' => $current,
            'past' => $past,
            'engine_upcoming' => $engine_upcoming,
            'engine_current' => $engine_current,
            'engine_past' => $engine_past,
        ]);
    }

    public function details(Request $request, $id){
        // https://travelta.online/agent/booking/details/{id}
        // invoice => /accounting/booking/invoice/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent = $this->affilate
            ->where('id', $agent_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent = $this->agent
            ->where('id', $agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $manuel_booking = $this->manuel_booking
        ->with([
            'payments.financial' => function ($query) {
                $query->select('id', 'name', 'logo');
            }
        ])
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->first();
        if (empty($manuel_booking)) {
            return response()->json([
                'errors' => 'Manuel booking not found'
            ], 400);
        }
        $traveler = null;
        $data = $manuel_booking->to_client;
        if (!empty($manuel_booking->to_supplier_id)) {
            $traveler['id'] = $data->id;
            $traveler['name'] = $data->agent;
            $traveler['phone'] = is_string($data->phones) ? json_decode($data->phones)[0] 
            ?? $data->phones: $data->phones[0];
            $traveler['email'] = is_string($data->emails) ? json_decode($data->emails)[0] 
            ?? $data->emails: $data->emails[0];
            $traveler['position'] = 'Supplier';
        }
        else{
            $traveler['id'] = $data->id;
            $traveler['name'] = $data->name;
            $traveler['phone'] = $data->phone;
            $traveler['email'] = $data->email;
            $traveler['position'] = 'Customer';
        }
        $payments = $manuel_booking->payments;
        $confirmation_tasks = $manuel_booking->tasks;
        $actions = [
            'confirmed' => $manuel_booking->operation_confirmed,
            'vouchered' => $manuel_booking->operation_vouchered,
            'canceled' => $manuel_booking->operation_canceled,
        ];
        $agent_data = [
            'name' => $agent->name,
            'email' => $agent->email,
            'phone' => $agent->phone,
        ];
        return response()->json([
            'traveler' => $traveler,
            'payments' => $payments,
            'actions' => $actions,
            'agent_data' => $agent_data,
            'confirmation_tasks' => $confirmation_tasks,
        ]);
    }

    public function special_request(Request $request, $id){
        // https://travelta.online/agent/booking/special_request/{id}
        // Keys
        // special_request
        $validation = Validator::make($request->all(), [
            'special_request' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'special_request' => $request->special_request
        ]);

        return response()->json([
            'success' => $request->special_request
        ]);
    } 

    // Booking Engine
    public function engine_details(Request $request, $id){
        // https://travelta.online/agent/booking/engine_details/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $affilate = $this->affilate
            ->where('id', $agent_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent = $this->agent
            ->where('id', $agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $booking_engine = $this->booking_engine
        // ->with([
        //     'payments.financial' => function ($query) {
        //         $query->select('id', 'name');
        //     }
        // ])
        ->where('id', $id)
        ->where($agent_type, $agent_id)
        ->first();
        if (empty($booking_engine)) {
            return response()->json([
                'errors' => 'booking engine not found'
            ], 400);
        }
        $traveler = null;
        $data = $booking_engine->to_client;
        if (!empty($booking_engine->to_supplier_id)) {
            $traveler['id'] = $data->id;
            $traveler['name'] = $data->agent;
            $traveler['phone'] = is_string($data->phones) ? json_decode($data->phones)[0] 
            ?? $data->phones: $data->phones[0];
            $traveler['email'] = is_string($data->emails) ? json_decode($data->emails)[0] 
            ?? $data->emails: $data->emails[0];
            $traveler['position'] = 'Supplier';
        }
        else{
            $traveler['id'] = $data->id;
            $traveler['name'] = $data->name;
            $traveler['phone'] = $data->phone;
            $traveler['email'] = $data->email;
            $traveler['position'] = 'Customer';
        }
        $confirmation_tasks = $booking_engine->tasks;
        //$payments = $booking_engine->payments;
        $actions = [
            'confirmed' => $booking_engine->operation_confirmed,
            'vouchered' => $booking_engine->operation_vouchered,
            'canceled' => $booking_engine->operation_canceled,
        ];
        $agent_data = [
            'name' => $agent->name,
            'email' => $agent->email,
            'phone' => $agent->phone,
        ];
        return response()->json([
            'traveler' => $traveler,
          //  'payments' => $payments,
            'actions' => $actions,
            'agent_data' => $agent_data,
            'confirmation_tasks' => $confirmation_tasks,
        ]);
    }

    public function engine_special_request(Request $request, $id){
        // https://travelta.online/agent/booking/engine_special_request/{id}
        // Keys
        // special_request
        $validation = Validator::make($request->all(), [
            'special_request' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $this->booking_engine
        ->where('id', $id)
        ->update([
            'special_request' => $request->special_request
        ]);

        return response()->json([
            'success' => $request->special_request
        ]);
    } 
}
