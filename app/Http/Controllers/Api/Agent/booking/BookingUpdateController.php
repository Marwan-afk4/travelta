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
        // to_supplier_id,to_customer_id,agent_sales_id,from_supplier_id,cost,
        // price,currency_id,tax_type,total_price,country_id,city_id,mark_up,mark_up_type,
        // special_request,
        // check_in ,check_out ,nights ,hotel_name ,room_type ,room_quantity ,adults ,childreen,
        //"data": {"to_supplier_id": null,"to_customer_id": "4","agent_sales_id": "3","from_supplier_id": "1","cost": "111","price": "111","currency_id": "1","tax_type": "exclude","total_price": "111","country_id": "1","city_id": "1","mark_up": "111","mark_up_type": "precentage","special_request": "HHH","check_in": "2024-01-01","check_out": "2025-01-01","nights": "3","hotel_name": "HHHH","room_type": "[\"Single\"]","room_quantity": "1","adults": "1","childreen": "1","adults_data": [{"title": "Ahmed","first_name": "Yahia","last_name": "Rizk"}],"file": {}}
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
                $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
                $manuel_booking->taxes()->sync($taxes);
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
                $adults_data = is_string($request->adults_data) ? json_decode($request->adults_data) : $request->adults_data; 
                foreach ($adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id, 
                        'title' => $item->title ?? $item['title'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $children_data = is_string($request->children_data) ? json_decode($request->children_data) : $request->children_data; 
                foreach ($children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item->age ?? $item['age'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
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
                $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
                $manuel_booking->taxes()->sync($taxes);
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
                $adults_data = is_string($request->adults_data) ? json_decode($request->adults_data) : $request->adults_data; 
                foreach ($adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id, 
                        'title' => $item->title ?? $item['title'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $children_data = is_string($request->children_data) ? json_decode($request->children_data) : $request->children_data; 
                foreach ($children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item->age ?? $item['age'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
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
                $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
                $manuel_booking->taxes()->sync($taxes);
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
                $adults_data = is_string($request->adults_data) ? json_decode($request->adults_data) : $request->adults_data; 
                foreach ($adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id, 
                        'title' => $item->title ?? $item['title'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $children_data = is_string($request->children_data) ? json_decode($request->children_data) : $request->children_data; 
                foreach ($children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item->age ?? $item['age'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
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
                $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
                $manuel_booking->taxes()->sync($taxes);
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
                $adults_data = is_string($request->adults_data) ? json_decode($request->adults_data) : $request->adults_data; 
                foreach ($adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id, 
                        'title' => $item->title ?? $item['title'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $children_data = is_string($request->children_data) ? json_decode($request->children_data) : $request->children_data; 
                foreach ($children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item->age ?? $item['age'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
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
                $taxes = is_string($request->taxes) ? json_decode($request->taxes) : $request->taxes;
                $manuel_booking->taxes()->sync($taxes);
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
                $tour_hotels = is_string($request->tour_hotels) ? json_decode($request->tour_hotels)
                : $request->tour_hotels;
                foreach ($tour_hotels as $item) {
                    $this->tour_hotel
                    ->create([
                        'manuel_tour_id' => $manuel_tour->id,
                        'hotel_name' => $item->hotel_name ?? $item['hotel_name'],
                        'room_type' => $item->room_type ?? $item['room_type'],
                        'check_in' => $item->check_in ?? $item['check_in'],
                        'check_out' => $item->check_out ?? $item['check_out'],
                        'nights' => $item->nights ?? $item['nights'],
                        'destination' => $item->destination ?? $item['destination'],
                    ]);
                }
            }
            if ($request->tour_buses) {
                $tour_buses = is_string($request->tour_buses) ? json_decode($request->tour_buses)
                : $request->tour_buses;
                foreach ($tour_buses as $item) {
                    $this->tour_bus
                    ->create([
                        'manuel_tour_id' => $manuel_tour->id,
                        'transportation' => $item->transportation ?? $item['transportation'],
                        'seats' => $item->seats ?? $item['seats'],
                        'departure' => $item->departure ?? $item['departure'],
                    ]);
                }
            }
            if ($request->adults_data) { 
                $adults_data = is_string($request->adults_data) ? json_decode($request->adults_data) : $request->adults_data; 
                foreach ($adults_data as $item) {
                    $this->adults
                    ->create([
                        'manuel_booking_id' => $id, 
                        'title' => $item->title ?? $item['title'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
                    ]);
                }
            }
            if ($request->children_data) {
                $children_data = is_string($request->children_data) ? json_decode($request->children_data) : $request->children_data; 
                foreach ($children_data as $item) {
                    $this->children
                    ->create([
                        'manuel_booking_id' => $id,
                        'age' => $item->age ?? $item['age'],
                        'first_name' => $item->first_name ?? $item['first_name'],
                        'last_name' => $item->last_name ?? $item['last_name'],
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
