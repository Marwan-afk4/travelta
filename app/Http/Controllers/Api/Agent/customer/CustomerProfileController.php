<?php

namespace App\Http\Controllers\Api\Agent\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BookingRequestResource;
use App\Http\Resources\ManuelBookingResource;
use Illuminate\Support\Facades\Validator;
use App\trait\image;

use App\Models\Customer;
use App\Models\CustomerData;
use App\Models\RequestBooking;
use App\Models\ManuelBooking;
use App\Models\LegalPaper;

class CustomerProfileController extends Controller
{
    public function __construct(private Customer $customer,
    private RequestBooking $request_booking, private ManuelBooking $manuel_booking,
    private LegalPaper $legal_papers, private CustomerData $customer_data){}
    use image;

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
        $customer_data = $this->customer_data
        ->where('customer_id', $id)
        ->where($role, $agent_id)
        ->first();
        $customer_info = $this->customer
        ->select('name', 'phone', 'email', 'gender', 'emergency_phone', 
        'watts', 'service_id', 'nationality_id', 'country_id',
        'city_id', 'image', 'created_at as date_added')
        ->with(['service:id,service_name', 
        'nationality:id,name', 'country:id,name', 'city:id,name'])
        ->where('id', $id)
        ->first();
        $customer_info->total_booking = $customer_data->total_booking;
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

    public function upload_papers(Request $request){
        // https://travelta.online/agent/customer/upload_papers
        // Keys
        // images[{image, type, customer_id}]
        // "images": [{"image": "data:image","type": "id","customer_id": "1"},{"image": "data:image/png;","type": "passport","customer_id": "1"}]

        $validation = Validator::make($request->all(), [
            'images' => 'required',
            'images.*.image' => 'required',
            'images.*.type' => 'required',
            'images.*.customer_id' => 'required|exists:customers,id',
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

        $images = $request->images;
        foreach ($images as $item) {
            $image = $this->storeBase64Image($item['image'], 'agent/customer/legal_papers');
            $this->legal_papers
            ->create([
                'image' => $image,
                'customer_id' => $item['customer_id'],
                'type' => $item['type'],
                $role => $agent_id,
            ]);
        }

        return response()->json([
            'success' => 'You add data success',
            'data' => $request->all()
        ]);
    }
}
