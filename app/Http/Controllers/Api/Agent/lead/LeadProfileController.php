<?php

namespace App\Http\Controllers\Api\Agent\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerData;
use App\Models\RequestBooking;

class LeadProfileController extends Controller
{
    public function __construct(private CustomerData $customer_data,
    private RequestBooking $request_booking){}

    public function profile(Request $request, $id){
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
        $customer_info = $this->customer_data
        ->select('name', 'phone', 'email', 'total_booking')
        ->where('customer_id', $id)
        ->where($role, $agent_id)
        ->first();
        $requests = $this->request_booking
        ->where($role, $agent_id)
        ->where('customer_id', $id)
        ->with(['hotel' => function($query){
            return $query->select('notes', 'hotel_name', 'check_in', 'check_out',
            'nights', 'room_type', 'adults', 'childreen');
        }, 'bus' => function($query){
            return $query->select('notes', 'from', 'to', 'departure',
            'arrival', 'adults', 'childreen', 'bus', 'bus_number', 'driver_phone');
        }, 'flight' => function($query){
            return $query->select('notes', 'type', 'direction', 'departure',
            'arrival', 'from_to', 'adults', 'childreen', 'infants', 'class',
            'airline', 'ticket_number', 'ref_pnr');
        }, 'tour' => function($query){
            return $query->select('notes', 'tour', 'type', 'adults', 'childreen')
            ->with('hotel', 'bus');
        }, 'visa' =>  function($query){
            return $query->select('notes', 'adults', 'childreen', 'country',
            'travel_date', 'appointment_date', 'notes');
        }])
        ->get();

        return response()->json([
            'customer_info' => $customer_info,
            'requests' => $requests,
        ]);
    }
}
