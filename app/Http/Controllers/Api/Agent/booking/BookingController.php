<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBusResource;
use App\Http\Resources\ManuelFlightResource;
use App\Http\Resources\ManuelHotelResource;
use App\Http\Resources\ManuelTourResource;
use App\Http\Resources\ManuelVisaResource;

use App\Models\Service;
use App\Models\ManuelBooking;

class BookingController extends Controller
{
    public function __construct(private Service $services, 
    private ManuelBooking $manuel_booking){}

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
        return response()->json([
            'upcoming' => $upcoming,
            'current' => $current,
            'past' => $past,
        ]);
    }

    public function details(){
        
    }
}
