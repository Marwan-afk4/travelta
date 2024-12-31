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

    public function upcoming($id){
        $service = $this->services
        ->where('id', $id)
        ->first()->service_name;
        $manuel_booking = [];
        if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'hotel'])
            ->where('from_service_id', $id)
            ->whereHas('hotel', function($query){
                $query->where('check_in', '>', date('Y-m-d'));
            })
            ->get();
        }
        elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'bus'])
            ->where('from_service_id', $id)
            ->whereHas('bus', function($query){
                $query->where('departure', '>', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'visa'])
            ->where('from_service_id', $id)
            ->whereHas('visa', function($query){
                $query->where('travel_date', '>', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'flight'])
            ->where('from_service_id', $id)
            ->whereHas('flight', function($query){
                $query->where('departure', '>', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'tour'])
            ->where('from_service_id', $id)
            ->whereNotHas('tour.hotel', function($query){
                $query->where('check_in', '<=', date('Y-m-d'));
            })
            ->get();
        }

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function current($id){
        $service = $this->services
        ->where('id', $id)
        ->first()->service_name;
        $manuel_booking = [];
        if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'hotel'])
            ->where('from_service_id', $id)
            ->whereHas('hotel', function($query){
                $query->whereDate('check_in', '<=', date('Y-m-d'))
                ->whereDate('check_out', '>=', date('Y-m-d'));
            })
            ->get();
        }
        elseif($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses'){
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'bus'])
            ->where('from_service_id', $id)
            ->whereHas('bus', function($query){
                $query->whereDate('departure', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'visa'])
            ->where('from_service_id', $id)
            ->whereHas('visa', function($query){
                $query->whereDate('travel_date', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'flight'])
            ->where('from_service_id', $id)
            ->whereHas('flight', function($query){
                $query->whereDate('departure', date('Y-m-d'));
            })
            ->get();
        }
        elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
            $manuel_booking = $this->manuel_booking
            ->with(['from_supplier' => function($query){
                $query->select('agent');
            }, 'tour'])
            ->where('from_service_id', $id)
            ->whereNotHas('tour.hotel', function($query){
                $query->whereDate('check_in', date('Y-m-d'));
            })
            ->get();
        }

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function past($id){
        $service = $this->services
        ->where('id', $id)
        ->first()->service_name;
    }
}
