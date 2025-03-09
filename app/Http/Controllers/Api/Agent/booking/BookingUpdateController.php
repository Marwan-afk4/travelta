<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use App\Models\ManuelBooking;
use App\Models\ManuelBus;
use App\Models\ManuelFlight;
use App\Models\ManuelHotel;
use App\Models\ManuelVisa;
use App\Models\ManuelTour;
use App\Models\ManuelTourBus;
use App\Models\ManuelTourHotel;
use App\Models\Child;
use App\Models\Adult;
use App\Models\BookingPayment;
use App\Models\ManuelCart;
use App\Models\FinantiolAcounting;
use App\Models\SupplierBalance;
use App\Models\PaymentsCart;
use App\Models\CustomerData;
use App\Models\SupplierAgent;
use App\Models\Agent;
use App\Models\AffilateAgent;

use App\Http\Requests\api\agent\manuel_booking\BookingRequest;
use App\Http\Requests\api\agent\manuel_booking\CartEditBookingRequest;
use App\Http\Requests\api\agent\manuel_booking\BusRequest;
use App\Http\Requests\api\agent\manuel_booking\FlightRequest;
use App\Http\Requests\api\agent\manuel_booking\HotelRequest;
use App\Http\Requests\api\agent\manuel_booking\TourRequest;
use App\Http\Requests\api\agent\manuel_booking\VisaRequest;

class BookingUpdateController extends Controller
{
    public function __construct(private ManuelBooking $manuel_booking,
    private ManuelBus $manuel_bus, private ManuelFlight $manuel_flight,
    private ManuelHotel $manuel_hotel, private ManuelVisa $manuel_visa,
    private ManuelTour $manuel_tour, private Adult $adults,
    private Child $children, private ManuelTourHotel $tour_hotel,
    private ManuelTourBus $tour_bus, private BookingPayment $booking_payment,
    private ManuelCart $manuel_cart, private FinantiolAcounting $financial_accounting,
    private PaymentsCart $payments_cart, private SupplierBalance $supplier_balance,
    private CustomerData $customer_data, private SupplierAgent $supplier_agent,
    private Agent $agent, private AffilateAgent $affilate_agent
    ){}

    public function hotel(Request $request, $id){
        // agent/booking/hotel/{id}
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
        else{
            $role = 'agent_id';
        }

        $manuel_booking = $this->manuel_booking
        ->where('id', $id)
        ->with('hotel', 'from_supplier', 'to_supplier', 'to_customer',
        'service', 'agent_sales', 'currency', 'adults', 'children')
        ->first();

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function flight(Request $request, $id){
        // agent/booking/flight/{id}
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
        else{
            $role = 'agent_id';
        }

        $manuel_booking = $this->manuel_booking
        ->where('id', $id)
        ->with('flight', 'from_supplier', 'to_supplier', 'to_customer',
        'service', 'agent_sales', 'currency', 'adults', 'children')
        ->first();

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function bus(Request $request, $id){
        // agent/booking/bus/{id}
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
        else{
            $role = 'agent_id';
        }

        $manuel_booking = $this->manuel_booking
        ->where('id', $id)
        ->with('bus', 'from_supplier', 'to_supplier', 'to_customer',
        'service', 'agent_sales', 'currency', 'adults', 'children')
        ->first();

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function visa(Request $request, $id){
        // agent/booking/visa/{id}
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
        else{
            $role = 'agent_id';
        }

        $manuel_booking = $this->manuel_booking
        ->where('id', $id)
        ->with('visa', 'from_supplier', 'to_supplier', 'to_customer',
        'service', 'agent_sales', 'currency', 'adults', 'children')
        ->first();

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function tour(Request $request, $id){
        // agent/booking/tour/{id}
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
        else{
            $role = 'agent_id';
        }

        $manuel_booking = $this->manuel_booking
        ->where('id', $id)
        ->with(['tour' => function($query){
            $query->with('hotel', 'bus');
        }, 'from_supplier', 'to_supplier', 'to_customer',
        'service', 'agent_sales', 'currency', 'adults', 'children'])
        ->first();

        return response()->json([
            'manuel_booking' => $manuel_booking
        ]);
    }

    public function update_hotel(BookingRequest $request, HotelRequest $hotel_request,
    CartEditBookingRequest $cart_request, $id){
        // agent/booking/update_hotel/{id}
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,payment_type,special_request,
        // check_in ,check_out ,nights ,hotel_name ,room_type ,room_quantity ,adults ,childreen,
        // payment_type, total_cart, cart_id
        // payment_methods[amount, payment_method_id]
        // payments [{amount, date}]
        // {"agent_sales_id": "4","from_supplier_id": "2","cost": "300","price": "200","currency_id": "3","total_price": "600","country_id": "2","mark_up": "22","mark_up_type": "value","payment_type": "partial","taxes": [    "3"],"adults_data": [    {"title": "R","first_name": "RR","last_name": "RRR"    }],"children_data": [    {"age": "22","first_name": "AA","last_name": "AAA"    }],"check_in": "2024-07-07","check_out": "2025-06-06","nights": "3","hotel_name": "SSS","room_type": "[\"SSSS\"]","room_quantity": "2","adults": "1","childreen": "1","to_supplier_id": "1","to_customer_id": null,"tax_type": "include","special_request": "SSS","total_cart": "2000","payment_methods": [    {"amount": "220","payment_method_id": "3"    }],"payments": [    {"amount": "250","date": "2025-04-04"    }]}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $validation = Validator::make($request->all(), [
            'taxes.*' => 'exists:taxes,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        try {
            $bookingRequest = $request->validated();
            $hotelRequest = $hotel_request->validated();
            $manuel_booking = $this->manuel_booking
            ->where('id', $id)
            ->first();
            $old_manuel_booking = clone $manuel_booking;
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_hotel
            ->where('manuel_booking_id', $id)
            ->update($hotelRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            if ($request->adults_data) {
                $validation = Validator::make($request->all(), [
                    'adults_data' => 'array',
                    'adults_data.*.title' => 'required',
                    'adults_data.*.first_name' => 'required',
                    'adults_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id,
                        'title' => $item['title'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $validation = Validator::make($request->all(), [
                    'children_data' => 'array',
                    'children_data.*.age' => 'required|numeric',
                    'children_data.*.first_name' => 'required',
                    'children_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item['age'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            // Cart
            $amount_payment = 0;
            $oldamount = 0;
            $booking_payments = $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->get();
            foreach ($booking_payments as $item) {
                $financial_accounting = $this->financial_accounting
                ->where('id', $item->financial_id)
                ->where($role, $agent_id)
                ->first();
                $oldamount += $item->amount;
                $financial_accounting->balance -= $item->amount;
                $financial_accounting->save();
            }
            $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->delete();
            if ($cart_request->payment_methods) {
                $payment_methods = is_string($cart_request->payment_methods) ? 
                json_decode($cart_request->payment_methods) : $cart_request->payment_methods;
                foreach ($payment_methods as $item) {
                    $amount_payment += $item->amount ?? $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $this->booking_payment
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'date' => date('Y-m-d'),
                        'amount' => $item->amount ?? $item['amount'],
                        'financial_id' => $item->payment_method_id ?? $item['payment_method_id'],
                        'code' => $code,
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id ,
                        'first_time' => 1,
                    ]);
                    $cartRequest = [
                        'total' => $cart_request->total_cart,
                        'payment' => $item->amount ?? $item['amount'],
                        'payment_method_id' => $item->payment_method_id ?? $item['payment_method_id'],
                    ];
                    $manuel_cart = $this->manuel_cart
                    ->where('manuel_booking_id', $manuel_booking->id)
                    ->update($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id ?? $item['payment_method_id'])
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + ($item->amount?? $item['amount']);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $this->booking_payment
                ->create([
                    'manuel_booking_id' => $manuel_booking->id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code,
                    $role => $agent_id,
                    'supplier_id' => $manuel_booking->to_supplier_id ,
                    'first_time' => 1,
                ]);
            }
            if ($cart_request->payment_type == 'partial' || $cart_request->payment_type == 'later') {
                $validation = Validator::make($cart_request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($cart_request->payments) ? json_decode($cart_request->payments)
                : $cart_request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount ?? $item['amount'],
                        'date' => $item->date ?? $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->where('status', 1)
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking - $oldamount,
                ]);
                $this->customers
                ->where('id', $manuel_booking->to_customer_id ?? null)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            }
            else{
                $customer = $this->supplier_agent
                ->where('id', $manuel_booking->to_supplier_id ?? null)
                ->first();
                $position = 'Supplier';
            }
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
            //............................
           if (isset($manuel_booking->to_supplier_id) && is_numeric($manuel_booking->to_supplier_id)) {
               $supplier_balance = $this->supplier_balance
               ->where('supplier_id', $manuel_booking->to_supplier_id)
               ->where('currency_id', $manuel_booking->currency_id ?? null)
               ->first();
               $old_balance = $this->supplier_balance
               ->where('supplier_id', $old_manuel_booking->to_supplier_id)
               ->where('currency_id', $old_manuel_booking->currency_id ?? null)
               ->first();

               if (!empty($old_balance)) {
                    $old_balance->update([
                        'balance' => $old_balance->balance + $old_manuel_booking->total_price
                    ]);
               }
               if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'balance' => -$manuel_booking->total_price,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
               }
               else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance - $manuel_booking->total_price
                    ]);
               }
           }
           if (isset($manuel_booking->from_supplier_id) && is_numeric($manuel_booking->from_supplier_id)) {
                $supplier_balance = $this->supplier_balance
                ->where('supplier_id', $manuel_booking->from_supplier_id)
                ->where('currency_id', $manuel_booking->currency_id ?? null)
                ->first();
                //old_manuel_booking
                $old_balance = $this->supplier_balance
                ->where('supplier_id', $old_manuel_booking->from_supplier_id)
                ->where('currency_id', $old_manuel_booking->currency_id ?? null)
                ->first();
                if (!empty($old_balance)) {
                     $old_balance->update([
                         'balance' => $old_balance->balance - $old_manuel_booking->cost
                     ]);
                }
                if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->from_supplier_id,
                        'balance' => $manuel_booking->cost,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
                }
                else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance + $manuel_booking->cost
                    ]);
                }
           }
           // __________________________________________________________________________
            return response()->json([
                'success' => 'You update data success',
                'data' => $request->all(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_flight(BookingRequest $request, FlightRequest $flight_request,
    CartEditBookingRequest $cart_request, $id){
        // agent/booking/update_flight/{id}
        // Keys
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,payment_type,special_request,
        // payment_type, total_cart, cart_id
        // payment_methods[amount, payment_method_id]
        // payments [{amount, date}]
        // type,direction,from_to,departure ,arrival ,class,adults,childreen,infants,airline,ticket_number,adult_price,child_price,ref_pnr
        // {"agent_sales_id": "4","from_supplier_id": "2","cost": "300","price": "200","currency_id": "3","total_price": "600","country_id": "2","mark_up": "22","mark_up_type": "value","payment_type": "partial","taxes": [    "3"],"adults_data": [    {"title": "R","first_name": "RR","last_name": "RRR"    }],"children_data": [    {"age": "22","first_name": "AA","last_name": "AAA"    }],"adults": "1","childreen": "1","to_supplier_id": "1","to_customer_id": null,"tax_type": "include","special_request": "SSS","total_cart": "2000","payment_methods": [    {"amount": "220","payment_method_id": "3"    }],"payments": [    {"amount": "250","date": "2025-04-04"    }],"type": "domestic","direction": "one_way","from_to": "[{\"from\":\"cairo\",\"to\":\"aswan\"}]","departure": "2025-08-08","arrival": "2025-09-09","class": "First","infants": "1","airline": "Nile","ticket_number": "3453434","adult_price": "22","child_price": "33","ref_pnr": "456"}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $validation = Validator::make($request->all(), [
            'taxes.*' => 'exists:taxes,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        try {
            $bookingRequest = $request->validated();
            $flightRequest = $flight_request->validated();
            $manuel_booking = $this->manuel_booking
            ->where('id', $id)
            ->first();
            
            $old_manuel_booking = clone $manuel_booking;
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_flight
            ->where('manuel_booking_id', $id)
            ->update($flightRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            if ($request->adults_data) {
                $validation = Validator::make($request->all(), [
                    'adults_data' => 'array',
                    'adults_data.*.title' => 'required',
                    'adults_data.*.first_name' => 'required',
                    'adults_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id,
                        'title' => $item['title'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $validation = Validator::make($request->all(), [
                    'children_data' => 'array',
                    'children_data.*.age' => 'required|numeric',
                    'children_data.*.first_name' => 'required',
                    'children_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item['age'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            // Cart
            $amount_payment = 0;
            $oldamount = 0;
            $booking_payments = $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->get();
            foreach ($booking_payments as $item) {
                $financial_accounting = $this->financial_accounting
                ->where('id', $item->financial_id)
                ->where($role, $agent_id)
                ->first();
                $oldamount += $item->amount;
                $financial_accounting->balance -= $item->amount;
                $financial_accounting->save();
            }
            $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->delete();
            if ($cart_request->payment_methods) {
                $payment_methods = is_string($cart_request->payment_methods) ? 
                json_decode($cart_request->payment_methods) : $cart_request->payment_methods;
                foreach ($payment_methods as $item) {
                    $amount_payment += $item->amount ?? $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $this->booking_payment
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'date' => date('Y-m-d'),
                        'amount' => $item->amount ?? $item['amount'],
                        'financial_id' => $item->payment_method_id ?? $item['payment_method_id'],
                        'code' => $code,
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id ,
                        'first_time' => 1,
                    ]);
                    $cartRequest = [
                        'total' => $cart_request->total_cart,
                        'payment' => $item->amount ?? $item['amount'],
                        'payment_method_id' => $item->payment_method_id ?? $item['payment_method_id'],
                    ];
                    $manuel_cart = $this->manuel_cart
                    ->where('manuel_booking_id', $manuel_booking->id)
                    ->update($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id ?? $item['payment_method_id'])
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + ($item->amount?? $item['amount']);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $this->booking_payment
                ->create([
                    'manuel_booking_id' => $manuel_booking->id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code,
                    $role => $agent_id,
                    'supplier_id' => $manuel_booking->to_supplier_id ,
                    'first_time' => 1,
                ]);
            }
            if ($cart_request->payment_type == 'partial' || $cart_request->payment_type == 'later') {
                $validation = Validator::make($cart_request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($cart_request->payments) ? json_decode($cart_request->payments)
                : $cart_request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount ?? $item['amount'],
                        'date' => $item->date ?? $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->where('status', 1)
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking - $oldamount,
                ]);
                $this->customers
                ->where('id', $manuel_booking->to_customer_id ?? null)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            }
            else{
                $customer = $this->supplier_agent
                ->where('id', $manuel_booking->to_supplier_id ?? null)
                ->first();
                $position = 'Supplier';
            }
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
            //............................
           if (isset($manuel_booking->to_supplier_id) && is_numeric($manuel_booking->to_supplier_id)) {
               $supplier_balance = $this->supplier_balance
               ->where('supplier_id', $manuel_booking->to_supplier_id)
               ->where('currency_id', $manuel_booking->currency_id ?? null)
               ->first();
               $old_balance = $this->supplier_balance
               ->where('supplier_id', $old_manuel_booking->to_supplier_id)
               ->where('currency_id', $old_manuel_booking->currency_id ?? null)
               ->first();

               if (!empty($old_balance)) {
                    $old_balance->update([
                        'balance' => $old_balance->balance + $old_manuel_booking->total_price
                    ]);
               }
               if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'balance' => -$manuel_booking->total_price,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
               }
               else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance - $manuel_booking->total_price
                    ]);
               }
           }
           if (isset($manuel_booking->from_supplier_id) && is_numeric($manuel_booking->from_supplier_id)) {
                $supplier_balance = $this->supplier_balance
                ->where('supplier_id', $manuel_booking->from_supplier_id)
                ->where('currency_id', $manuel_booking->currency_id ?? null)
                ->first();
                //old_manuel_booking
                $old_balance = $this->supplier_balance
                ->where('supplier_id', $old_manuel_booking->from_supplier_id)
                ->where('currency_id', $old_manuel_booking->currency_id ?? null)
                ->first();
                if (!empty($old_balance)) {
                     $old_balance->update([
                         'balance' => $old_balance->balance - $old_manuel_booking->cost
                     ]);
                }
                if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->from_supplier_id,
                        'balance' => $manuel_booking->cost,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
                }
                else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance + $manuel_booking->cost
                    ]);
                }
           }
           // __________________________________________________________________________
            return response()->json([
                'success' => 'You update data success',
                'data' => $request->all(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_bus(BookingRequest $request, BusRequest $bus_request,
    CartEditBookingRequest $cart_request, $id){
        // agent/booking/update_bus/{id}
        // Keys
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,payment_type,special_request,
        // payment_type, total_cart, cart_id
        // payment_methods[amount, payment_method_id]
        // payments [{amount, date}]
        // from, to, departure, arrival, adults, childreen, adult_price, bus, bus_number, driver_phone,
        // {"agent_sales_id": "4","from_supplier_id": "2","cost": "300","price": "200","currency_id": "3","total_price": "600","country_id": "2","mark_up": "22","mark_up_type": "value","payment_type": "partial","taxes": [    "3"],"adults_data": [    {"title": "R","first_name": "RR","last_name": "RRR"    }],"children_data": [    {"age": "22","first_name": "AA","last_name": "AAA"    }],"adults": "1","childreen": "2000","to_supplier_id": "1","to_customer_id": null,"tax_type": "include","special_request": "SSS","total_cart": "2000","payment_methods": [    {"amount": "220","payment_method_id": "3"    }],"payments": [    {"amount": "250","date": "2025-04-04"    }],"from": "Alex","to": "Cairo","departure": "2025-09-09","arrival": "2025-08-08","adult_price": "1","bus": "Nile","bus_number": "567556","driver_phone": "97897899789677"}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $validation = Validator::make($request->all(), [
            'taxes.*' => 'exists:taxes,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        try {
            $bookingRequest = $request->validated();
            $busRequest = $bus_request->validated();
            $manuel_booking = $this->manuel_booking
            ->where('id', $id)
            ->first();
            
            $old_manuel_booking = clone $manuel_booking;
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_bus
            ->where('manuel_booking_id', $id)
            ->update($busRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            if ($request->adults_data) {
                $validation = Validator::make($request->all(), [
                    'adults_data' => 'array',
                    'adults_data.*.title' => 'required',
                    'adults_data.*.first_name' => 'required',
                    'adults_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id,
                        'title' => $item['title'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $validation = Validator::make($request->all(), [
                    'children_data' => 'array',
                    'children_data.*.age' => 'required|numeric',
                    'children_data.*.first_name' => 'required',
                    'children_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item['age'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            // Cart
            $amount_payment = 0;
            $oldamount = 0;
            $booking_payments = $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->get();
            foreach ($booking_payments as $item) {
                $financial_accounting = $this->financial_accounting
                ->where('id', $item->financial_id)
                ->where($role, $agent_id)
                ->first();
                $oldamount += $item->amount;
                $financial_accounting->balance -= $item->amount;
                $financial_accounting->save();
            }
            $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->delete();
            if ($cart_request->payment_methods) {
                $payment_methods = is_string($cart_request->payment_methods) ? 
                json_decode($cart_request->payment_methods) : $cart_request->payment_methods;
                foreach ($payment_methods as $item) {
                    $amount_payment += $item->amount ?? $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $this->booking_payment
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'date' => date('Y-m-d'),
                        'amount' => $item->amount ?? $item['amount'],
                        'financial_id' => $item->payment_method_id ?? $item['payment_method_id'],
                        'code' => $code,
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id ,
                        'first_time' => 1,
                    ]);
                    $cartRequest = [
                        'total' => $cart_request->total_cart,
                        'payment' => $item->amount ?? $item['amount'],
                        'payment_method_id' => $item->payment_method_id ?? $item['payment_method_id'],
                    ];
                    $manuel_cart = $this->manuel_cart
                    ->where('manuel_booking_id', $manuel_booking->id)
                    ->update($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id ?? $item['payment_method_id'])
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + ($item->amount?? $item['amount']);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $this->booking_payment
                ->create([
                    'manuel_booking_id' => $manuel_booking->id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code,
                    $role => $agent_id,
                    'supplier_id' => $manuel_booking->to_supplier_id ,
                    'first_time' => 1,
                ]);
            }
            if ($cart_request->payment_type == 'partial' || $cart_request->payment_type == 'later') {
                $validation = Validator::make($cart_request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($cart_request->payments) ? json_decode($cart_request->payments)
                : $cart_request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount ?? $item['amount'],
                        'date' => $item->date ?? $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->where('status', 1)
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking - $oldamount,
                ]);
                $this->customers
                ->where('id', $manuel_booking->to_customer_id ?? null)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            }
            else{
                $customer = $this->supplier_agent
                ->where('id', $manuel_booking->to_supplier_id ?? null)
                ->first();
                $position = 'Supplier';
            }
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
            //............................
           if (isset($manuel_booking->to_supplier_id) && is_numeric($manuel_booking->to_supplier_id)) {
               $supplier_balance = $this->supplier_balance
               ->where('supplier_id', $manuel_booking->to_supplier_id)
               ->where('currency_id', $manuel_booking->currency_id ?? null)
               ->first();
               $old_balance = $this->supplier_balance
               ->where('supplier_id', $old_manuel_booking->to_supplier_id)
               ->where('currency_id', $old_manuel_booking->currency_id ?? null)
               ->first();

               if (!empty($old_balance)) {
                    $old_balance->update([
                        'balance' => $old_balance->balance + $old_manuel_booking->total_price
                    ]);
               }
               if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'balance' => -$manuel_booking->total_price,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
               }
               else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance - $manuel_booking->total_price
                    ]);
               }
           }
           if (isset($manuel_booking->from_supplier_id) && is_numeric($manuel_booking->from_supplier_id)) {
                $supplier_balance = $this->supplier_balance
                ->where('supplier_id', $manuel_booking->from_supplier_id)
                ->where('currency_id', $manuel_booking->currency_id ?? null)
                ->first();
                //old_manuel_booking
                $old_balance = $this->supplier_balance
                ->where('supplier_id', $old_manuel_booking->from_supplier_id)
                ->where('currency_id', $old_manuel_booking->currency_id ?? null)
                ->first();
                if (!empty($old_balance)) {
                     $old_balance->update([
                         'balance' => $old_balance->balance - $old_manuel_booking->cost
                     ]);
                }
                if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->from_supplier_id,
                        'balance' => $manuel_booking->cost,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
                }
                else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance + $manuel_booking->cost
                    ]);
                }
           }
           // __________________________________________________________________________
            return response()->json([
                'success' => 'You update data success',
                'data' => $request->all(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_visa(BookingRequest $request, VisaRequest $visa_request,
    CartEditBookingRequest $cart_request, $id){
        // agent/booking/update_visa/{id}
        // Keys
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,payment_type,special_request,
        // payment_type, total_cart, cart_id
        // payment_methods[amount, payment_method_id]
        // payments [{amount, date}]
        // country, travel_date, appointment_date, notes, childreen, adults, 
        // {"agent_sales_id": "4","from_supplier_id": "2","cost": "300","price": "200","currency_id": "3","total_price": "600","country_id": "2","mark_up": "22","mark_up_type": "value","payment_type": "partial","taxes": [    "3"],"adults_data": [    {        "title": "R",        "first_name": "RR",        "last_name": "RRR"    }],"children_data": [    {        "age": "22",        "first_name": "AA",        "last_name": "AAA"    }],"adults": "2","childreen": "2","to_supplier_id": "1","to_customer_id": null,"tax_type": "include","special_request": "SSS","total_cart": "2000","payment_methods": [    {        "amount": "220",        "payment_method_id": "3"    }],"payments": [    {        "amount": "250",        "date": "2025-04-04"    }],"country": "England","travel_date": "2025-09-09","appointment_date": "2025-09-09","notes": "5"}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $validation = Validator::make($request->all(), [
            'taxes.*' => 'exists:taxes,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        try {
            $bookingRequest = $request->validated();
            $visaRequest = $visa_request->validated();
            $manuel_booking = $this->manuel_booking
            ->where('id', $id)
            ->first();
            
            $old_manuel_booking = clone $manuel_booking;
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_visa
            ->where('manuel_booking_id', $id)
            ->update($visaRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            if ($request->adults_data) {
                $validation = Validator::make($request->all(), [
                    'adults_data' => 'array',
                    'adults_data.*.title' => 'required',
                    'adults_data.*.first_name' => 'required',
                    'adults_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id,
                        'title' => $item['title'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $validation = Validator::make($request->all(), [
                    'children_data' => 'array',
                    'children_data.*.age' => 'required|numeric',
                    'children_data.*.first_name' => 'required',
                    'children_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item['age'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            // Cart
            $amount_payment = 0;
            $oldamount = 0;
            $booking_payments = $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->get();
            foreach ($booking_payments as $item) {
                $financial_accounting = $this->financial_accounting
                ->where('id', $item->financial_id)
                ->where($role, $agent_id)
                ->first();
                $oldamount += $item->amount;
                $financial_accounting->balance -= $item->amount;
                $financial_accounting->save();
            }
            $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->delete();
            if ($cart_request->payment_methods) {
                $payment_methods = is_string($cart_request->payment_methods) ? 
                json_decode($cart_request->payment_methods) : $cart_request->payment_methods;
                foreach ($payment_methods as $item) {
                    $amount_payment += $item->amount ?? $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $this->booking_payment
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'date' => date('Y-m-d'),
                        'amount' => $item->amount ?? $item['amount'],
                        'financial_id' => $item->payment_method_id ?? $item['payment_method_id'],
                        'code' => $code,
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id ,
                        'first_time' => 1,
                    ]);
                    $cartRequest = [
                        'total' => $cart_request->total_cart,
                        'payment' => $item->amount ?? $item['amount'],
                        'payment_method_id' => $item->payment_method_id ?? $item['payment_method_id'],
                    ];
                    $manuel_cart = $this->manuel_cart
                    ->where('manuel_booking_id', $manuel_booking->id)
                    ->update($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id ?? $item['payment_method_id'])
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + ($item->amount?? $item['amount']);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $this->booking_payment
                ->create([
                    'manuel_booking_id' => $manuel_booking->id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code,
                    $role => $agent_id,
                    'supplier_id' => $manuel_booking->to_supplier_id ,
                    'first_time' => 1,
                ]);
            }
            if ($cart_request->payment_type == 'partial' || $cart_request->payment_type == 'later') {
                $validation = Validator::make($cart_request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($cart_request->payments) ? json_decode($cart_request->payments)
                : $cart_request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount ?? $item['amount'],
                        'date' => $item->date ?? $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->where('status', 1)
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking - $oldamount,
                ]);
                $this->customers
                ->where('id', $manuel_booking->to_customer_id ?? null)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            }
            else{
                $customer = $this->supplier_agent
                ->where('id', $manuel_booking->to_supplier_id ?? null)
                ->first();
                $position = 'Supplier';
            }
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
            //............................
           if (isset($manuel_booking->to_supplier_id) && is_numeric($manuel_booking->to_supplier_id)) {
               $supplier_balance = $this->supplier_balance
               ->where('supplier_id', $manuel_booking->to_supplier_id)
               ->where('currency_id', $manuel_booking->currency_id ?? null)
               ->first();
               $old_balance = $this->supplier_balance
               ->where('supplier_id', $old_manuel_booking->to_supplier_id)
               ->where('currency_id', $old_manuel_booking->currency_id ?? null)
               ->first();

               if (!empty($old_balance)) {
                    $old_balance->update([
                        'balance' => $old_balance->balance + $old_manuel_booking->total_price
                    ]);
               }
               if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'balance' => -$manuel_booking->total_price,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
               }
               else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance - $manuel_booking->total_price
                    ]);
               }
           }
           if (isset($manuel_booking->from_supplier_id) && is_numeric($manuel_booking->from_supplier_id)) {
                $supplier_balance = $this->supplier_balance
                ->where('supplier_id', $manuel_booking->from_supplier_id)
                ->where('currency_id', $manuel_booking->currency_id ?? null)
                ->first();
                //old_manuel_booking
                $old_balance = $this->supplier_balance
                ->where('supplier_id', $old_manuel_booking->from_supplier_id)
                ->where('currency_id', $old_manuel_booking->currency_id ?? null)
                ->first();
                if (!empty($old_balance)) {
                     $old_balance->update([
                         'balance' => $old_balance->balance - $old_manuel_booking->cost
                     ]);
                }
                if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->from_supplier_id,
                        'balance' => $manuel_booking->cost,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
                }
                else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance + $manuel_booking->cost
                    ]);
                }
           }
           // __________________________________________________________________________
            return response()->json([
                'success' => 'You update data success',
                'data' => $request->all(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_tour(BookingRequest $request, TourRequest $tour_request,
    CartEditBookingRequest $cart_request, $id){
        // agent/booking/update_tour/{id}
        // Keys
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,payment_type,special_request,
        // payment_type, total_cart, cart_id
        // payment_methods[amount, payment_method_id]
        // payments [{amount, date}]
        // tour,type,adult_price,child_price,adults,childreen,flight_date,
        // tour_buses[{transportation, seats, departure}]
        // tour_hotels[{hotel_name, room_type, check_in, check_out, nights, destination}]
        // {"agent_sales_id": "4","from_supplier_id": "2","cost": "300","price": "200","currency_id": "3","total_price": "600","country_id": "2","mark_up": "22","mark_up_type": "value","payment_type": "partial","taxes": [    "3"],"adults_data": [    {        "title": "R",        "first_name": "RR",        "last_name": "RRR"    }],"children_data": [    {        "age": "22",        "first_name": "AA",        "last_name": "AAA"    }],"adults": "1","childreen": "1","to_supplier_id": "1","to_customer_id": null,"tax_type": "include","special_request": "SSS","total_cart": "2000","payment_methods": [    {        "amount": "220",        "payment_method_id": "3"    }],"payments": [    {        "amount": "250",        "date": "2025-04-04"    }],"tour": "Ahlam","type": "domestic","adult_price": "20","child_price": "200","flight_date": "2025-09-09","tour_buses": [    {        "transportation": "Bus",        "seats": "33",        "departure": "2024-07-07"    }],"tour_hotels": [    {        "hotel_name": "Hilton",        "room_type": "Duplicated",        "check_in": "2025-06-06",        "check_out": "2025-09-09",        "nights": "4",        "destination": "Alex"    }]}        
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {
            $role = 'affilate_id';
        }
        else{
            $role = 'agent_id';
        }
        $validation = Validator::make($request->all(), [
            'taxes.*' => 'exists:taxes,id',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        try {
            $bookingRequest = $request->validated();
            $tourRequest = $tour_request->validated();
            $manuel_booking = $this->manuel_booking
            ->where('id', $id)
            ->first();
            
            $old_manuel_booking = clone $manuel_booking;
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $manuel_tour = $this->manuel_tour
            ->where('manuel_booking_id', $id)
            ->first();
            $manuel_tour->update($tourRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->tour_hotel
            ->where('manuel_tour_id', $id)
            ->delete();
            $this->tour_bus
            ->where('manuel_tour_id', $id)
            ->delete();
            if ($request->tour_hotels) {
                $validation = Validator::make($request->all(), [
                    'tour_hotels.*.hotel_name' => 'required', 
                    'tour_hotels.*.room_type' => 'required', 
                    'tour_hotels.*.check_in' => 'required|date', 
                    'tour_hotels.*.check_out' => 'required|date', 
                    'tour_hotels.*.nights' => 'required', 
                    'tour_hotels.*.destination' => 'required', 
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->tour_hotels as $item) {
                    $this->tour_hotel
                    ->create([
                        'manuel_tour_id' => $manuel_tour->id,
                        'hotel_name' => $item['hotel_name'],
                        'room_type' => $item['room_type'],
                        'check_in' => $item['check_in'],
                        'check_out' => $item['check_out'],
                        'nights' => $item['nights'],
                        'destination' => $item['destination'],
                    ]);
                }
            }
            if ($request->tour_buses) {
                $validation = Validator::make($request->all(), [
                    'tour_buses.*.transportation' => 'required',
                    'tour_buses.*.seats' => 'required|numeric',
                    'tour_buses.*.departure' => 'required|date',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->tour_buses as $item) {
                    $this->tour_bus
                    ->create([
                        'manuel_tour_id' => $manuel_tour->id,
                        'transportation' => $item['transportation'],
                        'seats' => $item['seats'],
                        'departure' => $item['departure'],
                    ]);
                }
            }
            if ($request->adults_data) {
                $validation = Validator::make($request->all(), [
                    'adults_data' => 'array',
                    'adults_data.*.title' => 'required',
                    'adults_data.*.first_name' => 'required',
                    'adults_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id,
                        'title' => $item['title'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $validation = Validator::make($request->all(), [
                    'children_data' => 'array',
                    'children_data.*.age' => 'required|numeric',
                    'children_data.*.first_name' => 'required',
                    'children_data.*.last_name' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                foreach ($request->children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item['age'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                    ]);
                }
            }
            // Cart
            $amount_payment = 0;
            $oldamount = 0;
            $booking_payments = $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->get();
            foreach ($booking_payments as $item) {
                $financial_accounting = $this->financial_accounting
                ->where('id', $item->financial_id)
                ->where($role, $agent_id)
                ->first();
                $oldamount += $item->amount;
                $financial_accounting->balance -= $item->amount;
                $financial_accounting->save();
            }
            $this->booking_payment
            ->where('manuel_booking_id', $manuel_booking->id)
            ->where('first_time', 1)
            ->delete();
            if ($cart_request->payment_methods) {
                $payment_methods = is_string($cart_request->payment_methods) ? 
                json_decode($cart_request->payment_methods) : $cart_request->payment_methods;
                foreach ($payment_methods as $item) {
                    $amount_payment += $item->amount ?? $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $this->booking_payment
                    ->create([
                        'manuel_booking_id' => $manuel_booking->id,
                        'date' => date('Y-m-d'),
                        'amount' => $item->amount ?? $item['amount'],
                        'financial_id' => $item->payment_method_id ?? $item['payment_method_id'],
                        'code' => $code,
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id ,
                        'first_time' => 1,
                    ]);
                    $cartRequest = [
                        'total' => $cart_request->total_cart,
                        'payment' => $item->amount ?? $item['amount'],
                        'payment_method_id' => $item->payment_method_id ?? $item['payment_method_id'],
                    ];
                    $manuel_cart = $this->manuel_cart
                    ->where('manuel_booking_id', $manuel_booking->id)
                    ->update($cartRequest);
                    $financial_accounting = $this->financial_accounting
                    ->where('id', $item->payment_method_id ?? $item['payment_method_id'])
                    ->where($role, $agent_id)
                    ->first();
                    $financial_accounting->balance = $financial_accounting->balance + ($item->amount?? $item['amount']);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $this->booking_payment
                ->create([
                    'manuel_booking_id' => $manuel_booking->id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code,
                    $role => $agent_id,
                    'supplier_id' => $manuel_booking->to_supplier_id ,
                    'first_time' => 1,
                ]);
            }
            if ($cart_request->payment_type == 'partial' || $cart_request->payment_type == 'later') {
                $validation = Validator::make($cart_request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($cart_request->payments) ? json_decode($cart_request->payments)
                : $cart_request->payments;
                foreach ($payments as $item) {
                    $this->payments_cart
                    ->create([
                        $role => $agent_id,
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'manuel_booking_id' => $manuel_booking->id,
                        'amount' => $item->amount ?? $item['amount'],
                        'date' => $item->date ?? $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->where('status', 1)
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking - $oldamount,
                ]);
                $this->customers
                ->where('id', $manuel_booking->to_customer_id ?? null)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            }
            else{
                $customer = $this->supplier_agent
                ->where('id', $manuel_booking->to_supplier_id ?? null)
                ->first();
                $position = 'Supplier';
            }
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
            //............................
           if (isset($manuel_booking->to_supplier_id) && is_numeric($manuel_booking->to_supplier_id)) {
               $supplier_balance = $this->supplier_balance
               ->where('supplier_id', $manuel_booking->to_supplier_id)
               ->where('currency_id', $manuel_booking->currency_id ?? null)
               ->first();
               $old_balance = $this->supplier_balance
               ->where('supplier_id', $old_manuel_booking->to_supplier_id)
               ->where('currency_id', $old_manuel_booking->currency_id ?? null)
               ->first();

               if (!empty($old_balance)) {
                    $old_balance->update([
                        'balance' => $old_balance->balance + $old_manuel_booking->total_price
                    ]);
               }
               if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->to_supplier_id,
                        'balance' => -$manuel_booking->total_price,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
               }
               else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance - $manuel_booking->total_price
                    ]);
               }
           }
           if (isset($manuel_booking->from_supplier_id) && is_numeric($manuel_booking->from_supplier_id)) {
                $supplier_balance = $this->supplier_balance
                ->where('supplier_id', $manuel_booking->from_supplier_id)
                ->where('currency_id', $manuel_booking->currency_id ?? null)
                ->first();
                //old_manuel_booking
                $old_balance = $this->supplier_balance
                ->where('supplier_id', $old_manuel_booking->from_supplier_id)
                ->where('currency_id', $old_manuel_booking->currency_id ?? null)
                ->first();
                if (!empty($old_balance)) {
                     $old_balance->update([
                         'balance' => $old_balance->balance - $old_manuel_booking->cost
                     ]);
                }
                if (empty($supplier_balance)) {
                    $this->supplier_balance
                    ->create([
                        'supplier_id' => $manuel_booking->from_supplier_id,
                        'balance' => $manuel_booking->cost,
                        'currency_id' => $manuel_booking->currency_id ?? null,
                    ]); 
                }
                else{
                    $supplier_balance->update([
                        'balance' => $supplier_balance->balance + $manuel_booking->cost
                    ]);
                }
           }
           // __________________________________________________________________________
            return response()->json([
                'success' => 'You update data success',
                'data' => $request->all(),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
}
