<?php

namespace App\Http\Controllers\Api\Agent\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BookingRequestResource;
use App\Http\Resources\ManuelBookingResource;

use App\Models\CustomerData;
use App\Models\RequestBooking;
use App\Models\ManuelBooking;
use App\Models\LegalPaper;

class CustomerProfileController extends Controller
{
    public function __construct(private CustomerData $customer_data,
    private RequestBooking $request_booking, private ManuelBooking $manuel_booking,
    private LegalPaper $legal_papers){}

    public function profile(Request $request, $id){
        // https://travelta.online/agent/customer/profile/{id}
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
        ->with(['hotel', 'bus', 'flight', 'tour' => function($query){
            return $query->with('hotel', 'bus');
        }, 'visa', 'customer', 'admin_agent', 'currency', 'service'])
        ->get();
        $manuel_booking = $this->manuel_booking
        ->with('from_supplier', 'country', 'hotel', 'bus',
        'flight', 'tour', 'visa')
        ->where('to_customer_id', $id)
        ->where($role, $agent_id)
        ->get();
        $requests = BookingRequestResource::collection($requests);
        $manuel_booking = ManuelBookingResource::collection($manuel_booking);
        $legal_papers = $this->legal_papers
        ->where('customer_id', $id)
        ->get();

        return response()->json([
            'customer_info' => $customer_info,
            'requests' => $requests,
            'manuel_booking' => $manuel_booking,
            'legal_papers' => $legal_papers,
        ]);
    }
}
