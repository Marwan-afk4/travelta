<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function upcoming(Request $request){
        // https://travelta.online/agent/booking/upcoming 
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
        $hotel = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'hotel', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->where('check_in', '>', date('Y-m-d'));
        })
        ->get();
        $bus = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'bus', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        })
        ->get();
        $visa = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'visa', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->where('travel_date', '>', date('Y-m-d'));
        })
        ->get();
        $flight = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'flight', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        })
        ->get();
       $tour = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'tour', 'taxes', 'from_supplier', 'to_client'])
        ->whereHas('tour.hotel')
        ->where($agent_type, $agent_id)
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_in', '<=', date('Y-m-d'));
        })
        ->get();

        return response()->json([
            'hotel' => $hotel,
            'bus' => $bus,
            'visa' => $visa,
            'flight' => $flight,
            'tour' => $tour,
        ]);
    }

    public function current(Request $request){
        // https://travelta.online/agent/booking/current
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
        $hotel = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'hotel', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'))
            ->whereDate('check_out', '>=', date('Y-m-d'));
        })
        ->get(); 
        $bus = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'bus', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'))
            ->whereDate('arrival', '>=', date('Y-m-d'));
        })
        ->get();
        $visa = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'visa', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->whereDate('travel_date', date('Y-m-d'));
        })
        ->get(); 
        $flight = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'flight', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'))
            ->whereDate('arrival', '>=', date('Y-m-d'));
        })
        ->get(); 
        $tour = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'tour.hotel', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'))
            ->whereDate('check_out', '>=', date('Y-m-d'));
        })
        ->get(); 

        return response()->json([
            'hotel' => $hotel,
            'bus' => $bus,
            'visa' => $visa,
            'flight' => $flight,
            'tour' => $tour,
        ]);
    }

    public function past(Request $request){
        // https://travelta.online/agent/booking/past 
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
        $hotel = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'hotel', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->where('check_out', '<', date('Y-m-d'));
        })
        ->get();
         $bus = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'bus', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->where('arrival', '<', date('Y-m-d'));
        })
        ->get();
        $visa = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'visa', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->where('travel_date', '<', date('Y-m-d'));
        })
        ->get();
        $flight = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'flight', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->where('arrival', '<', date('Y-m-d'));
        })
        ->get();
        $tour = $this->manuel_booking
        ->with(['from_supplier' => function($query){
            $query->select('agent');
        }, 'tour', 'taxes', 'from_supplier', 'to_client'])
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel')
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_out', '>=', date('Y-m-d'));
        })
        ->get();

        return response()->json([
            'hotel' => $hotel,
            'bus' => $bus,
            'visa' => $visa,
            'flight' => $flight,
            'tour' => $tour,
        ]);
    }
}
