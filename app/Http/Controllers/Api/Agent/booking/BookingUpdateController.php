<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;
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

use App\Http\Requests\api\agent\manuel_booking\BookingRequest;
use App\Http\Requests\api\agent\manuel_booking\CartBookingRequest;
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
    private PaymentsCart $payments_cart, private SupplierBalance $supplier_balance
    ){}

    public function update_hotel(BookingRequest $request, HotelRequest $hotel_request,
    CartBookingRequest $cart_request, $id){
        // agent/booking/update_hotel/{id}
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

            // ___________________________________________________________________
            
            // Cart
            // payment_type, total_cart, cart_id
            // payment_methods[amount, payment_method_id, image]
            // payments [{amount, date}]
            // "payment_type":"full","total_cart":"1","payment_methods":"[{\"amount\":200,\"payment_method_id\":9,\"image\":\"\"}]","payments":"[{\"amount\":400,\"date\":\"2025-05-05\"}]","cart_id":"67"}
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
                'success' => 'You update data success'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_flight(BookingRequest $request, FlightRequest $flight_request,
    CartBookingRequest $cart_request, $id){
        
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

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_bus(BookingRequest $request, BusRequest $bus_request,
    CartBookingRequest $cart_request, $id){
        
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
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_bus
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

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_visa(BookingRequest $request, VisaRequest $visa_request,
    CartBookingRequest $cart_request, $id){
        
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
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_visa
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

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
    
    public function update_tour(BookingRequest $request, TourRequest $tour_request,
    CartBookingRequest $cart_request, $id){
        // "tour_buses" => is_string($manuel_data_cart->tour_buses) 
        // ? json_decode($manuel_data_cart->tour_buses ?? '[]') ?? []:
        // $manuel_data_cart->tour_buses, 
        // "tour_hotels" => is_string($manuel_data_cart->tour_hotels) 
        // ? json_decode($manuel_data_cart->tour_hotels ?? '[]') ?? []:
        // $manuel_data_cart->tour_hotels, 
        // "adults_data" =>  is_string($manuel_data_cart->adults_data) 
        // ? json_decode($manuel_data_cart->adults_data ?? '[]') ?? []:
        // $manuel_data_cart->adults_data,
        // "children_data" =>  is_string($manuel_data_cart->children_data) 
        // ? json_decode($manuel_data_cart->children_data ?? '[]') ?? []:
        // $manuel_data_cart->children_data,
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
            $manuel_booking->update($bookingRequest);
            if ($request->taxes) {
                $manuel_booking->taxes()->sync($request->taxes);
            }
            $this->manuel_tour
            ->where('manuel_booking_id', $id)
            ->update($hotelRequest);
            $this->adults
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->children
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->tour_hotel
            ->where('manuel_booking_id', $id)
            ->delete();
            $this->tour_bus
            ->where('manuel_booking_id', $id)
            ->delete();
            if ($request->tour_hotels) {
                foreach ($request->tour_buses as $item) {
                    $this->tour_hotel
                    ->create([
                        'manuel_tour_id' => $id,
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
                foreach ($request->tour_buses as $item) {
                    $this->tour_bus
                    ->create([
                        'manuel_tour_id' => $id,
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

            return response()->json([
                'success' => 'You update data success'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'faild' => $e,
            ], 400);
        }
    }
}
