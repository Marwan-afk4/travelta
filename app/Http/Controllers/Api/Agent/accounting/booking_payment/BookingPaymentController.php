<?php

namespace App\Http\Controllers\Api\Agent\accounting\booking_payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBookingResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;
use App\Http\Resources\ManuelBusResource;
use App\Http\Resources\ManuelFlightResource;
use App\Http\Resources\ManuelHotelResource;
use App\Http\Resources\ManuelTourResource;
use App\Http\Resources\ManuelVisaResource;
use App\Http\Resources\EngineHotelResource;

use App\Models\ManuelBooking;
use App\Models\FinantiolAcounting;
use App\Models\BookingPayment;
use App\Models\PaymentsCart;
use App\Models\Agent;
use App\Models\AffilateAgent;
use App\Models\CustomerData;

class BookingPaymentController extends Controller
{
    public function __construct(private ManuelBooking $manuel_bookings,
    private FinantiolAcounting $financial_accounting, private BookingPayment $booking_payment,
    private PaymentsCart $payment_cart, private Agent $agent, private AffilateAgent $affilate_agent,
    private CustomerData $customer_data){}

    public function search(Request $request){
        // /accounting/booking/search
        // Keys
        // code
        $validation = Validator::make($request->all(), [
            'code' => 'required',
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
        $booking = $this->manuel_bookings
        ->with(['hotel', 'bus', 'flight', 'tour', 'visa', 'adults', 'children', 
        'payments.financial'])
        ->where('code', $request->code )
        ->first();
        if (empty($booking)) {
            return response()->json([
                'errors' => [
                    'code' => 'Code is wrong'
                ]
            ], 400);
        }
        $data = collect([]);
        if (!empty($booking)) {
            $data['id'] = $booking->id;
            $data['to_client'] = $booking->to_client->name;
            $data['code'] = $booking->code;
            $data['to_phone'] = $booking->to_client->phones[0] ?? $booking->to_client->phones ?? $booking->to_client->phone;
            $data['to_email'] = $booking->to_client->emails[0] ?? $booking->to_client->emails ?? $booking->to_client->email;

            $data['from_name'] = $booking->from_supplier->agent ?? null;
            $data['from_phone'] = $booking->from_supplier->phones[0] ?? $booking->from_supplier->phones ?? null;
            $data['from_email'] = $booking->from_supplier->emails[0] ?? $booking->from_supplier->emails ?? null;

            $data['no_adults'] = $booking->adults->count();
            $data['no_children'] = $booking->children->count();
            $data['hotel'] = $booking->hotel;
            $data['bus'] = $booking->bus;
            $data['flight'] = $booking->flight;
            $data['tour'] = $booking->tour;
            $data['visa'] = $booking->visa;
        }
        $financial_accounting = $this->financial_accounting 
        ->where($role, $agent_id)
        ->where('currency_id', $booking->currency_id )
        ->where('status', 1)
        ->get();
        $due_payment = $booking->payments_cart
        ->where('date', '<=', date('Y-m-d'))
        ->sum('due_payment');
        $remaining_payment = $booking->payments_cart
        ->sum('due_payment');
        $payments = $booking->payments;
        $payments = $payments->map(function($item){
            return [ 
                "id" => $item->id, 
                "amount" => $item->amount,
                "date" => $item->date, 
                "code" => $item->code, 
                "financial" => $item?->financial?->name ?? null,
            ];
        });
        $remaining_list = $booking->payments_cart
        ->select('id', 'date', 'due_payment')
        ->where('due_payment', '>', 0);

        return response()->json([
            'booking' => $data,
            'financial_accounting' => $financial_accounting,
            'currency' => $booking->currency->name,
            'total' => $booking->manuel_cart[0]?->total ?? $booking->total_price,
            'paid' => ($booking->manuel_cart[0]?->payment ?? 0) + ($booking->payments_cart->sum('payment')),
            'due_payment' => $due_payment,
            'remaining_payment' => $remaining_payment,
            'payments' => $payments,
            'remaining_list' => array_values($remaining_list->toArray()),
        ]);
    }
    
    public function invoice(Request $request, $id){
        // /accounting/booking/invoice/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent = $this->affilate_agent
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
        $booking_payment = $this->booking_payment
        ->where('id', $id)
        ->with('financial')
        ->first();
        $client = [];
        $manuel_booking = clone $booking_payment->manuel_booking;
        if (!empty($manuel_booking->to_supplier_id)) {
            $client_data = $manuel_booking->to_client;
            $client['name'] = $client_data->name;
            $client['phone'] = $client_data->phones[0] ?? $client_data->phones;
            $client['email'] = $client_data->emails[0] ?? $client_data->emails;
        }
        else{
            $client_data = $manuel_booking->to_client;
            $client['name'] = $client_data->name;
            $client['phone'] = $client_data->phone;
            $client['email'] = $client_data->email;
        }
        $service = $manuel_booking->service->service_name;
        $manuel_booking->from_supplier;
        $manuel_booking->country;
        $manuel_booking->bus;
        $manuel_booking->hotel;
        $manuel_booking->flight;
        $manuel_booking->tour;
        $manuel_booking->visa; 
        if ($service == 'hotel' || $service == 'Hotel' || $service == 'hotels' || $service == 'Hotels') {
            $hotel = ManuelHotelResource::collection([$manuel_booking]);
            $visa = null;
            $bus = null;
            $flight = null;
            $tour = null;
        }
        elseif ($service == 'visa' || $service == 'Visa' || $service == 'visas' || $service == 'Visas') {
            $visa = ManuelVisaResource::collection([$manuel_booking]);
            $hotel = null;
            $bus = null;
            $flight = null;
            $tour = null;
        }
        elseif ($service == 'bus' || $service == 'Bus' || $service == 'buses' || $service == 'Buses') {
            $bus = ManuelBusResource::collection([$manuel_booking]);
            $hotel = null;
            $visa = null;
            $flight = null;
            $tour = null;
        }
        elseif ($service == 'flight' || $service == 'Flight' || $service == 'flights' || $service == 'Flights') {
            $flight = ManuelFlightResource::collection([$manuel_booking]);
            $hotel = null;
            $visa = null;
            $bus = null;
            $tour = null;
        }
        elseif ($service == 'tour' || $service == 'Tour' || $service == 'tours' || $service == 'Tours') {
            $tour = ManuelTourResource::collection([$manuel_booking]);
            $hotel = null;
            $visa = null;
            $bus = null;
            $flight = null;
        }
        $booking_payment->makeHidden('manuel_booking');

        $agent_data = [
            'name' => $agent->name,
            'email' => $agent->email,
            'phone' => $agent->phone,
        ];
        $agent_data = [
            'name' => $agent->name,
            'email' => $agent->email,
            'phone' => $agent->phone,
        ];
        return response()->json([
            'booking_payment' => $booking_payment,
            'client' => $client,
            'agent_data' => $agent_data,
            'hotel' => $hotel[0] ?? null,
            'bus' => $bus[0] ?? null,
            'flight' => $flight[0] ?? null,
            'visa' => $visa[0] ?? null,
            'tour' => $tour[0] ?? null,
        ]);
    }

    public function add_payment(Request $request){
        // /accounting/booking/payment
        // Keys
        // manuel_booking_id
        // payments[date, amount, financial_accounting_id]
        $validation = Validator::make($request->all(), [
            'manuel_booking_id' => ['required', 'exists:manuel_bookings,id'],
            'payments' => ['required'],
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 401);
        }
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
        else {
            $role = 'agent_id';
        }
        $amount_payment = 0;
        $payments = is_string($request->payments) ? json_decode($request->payments): $request->payments;
        foreach ($payments as $item) {
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
            $manuel_bookings = $this->manuel_bookings
            ->where('id', $request->manuel_booking_id)
            ->first();
            $booking_payment = $this->booking_payment
            ->create([
                'manuel_booking_id' => $request->manuel_booking_id,
                'date' => date('Y-m-d'),
                'amount' => $item->amount,
                'financial_id' => $item->financial_accounting_id,
                'code' => $code,
                $role => $agent_id,
                'supplier_id' => $manuel_bookings->to_supplier_id ,
            ]);
            $payment_carts = $this->payment_cart
            ->where('manuel_booking_id', $request->manuel_booking_id)
            ->orderBy('date')
            ->get();
            $amount = $item->amount;
            $amount_payment += $amount;
            foreach ($payment_carts as $element) {
                if ($element->due_payment <= $amount) {
                    $this->payment_cart
                    ->where('id', $element->id)
                    ->update([
                        'payment' => $element->amount,
                        'status' => 'approve',
                    ]);
                    $amount -= $element->due_payment;
                }
                elseif ($amount > 0) {
                    $this->payment_cart
                    ->where('id', $element->id)
                    ->update([
                        'payment' => $amount + $element->payment,
                        'status' => 'approve',
                    ]);
                    $amount = 0;
                }
                else{
                    break;
                }
            }
        }
        $manuel_booking = $booking_payment->manuel_booking;
        $customer = $manuel_booking->to_client;
        if (!empty($manuel_booking->to_customer_id )) { 
            $position = 'Customer';
            $customer = $this->customer_data
            ->where('customer_id', $manuel_booking->to_customer_id ?? null)
            ->where($role, $agent_id)
            ->first();
            $customer->update([
                'total_booking' => $amount_payment + $customer->total_booking,
            ]);
        }
        else{ 
            $position = 'Supplier';
        } 
        $data = [];
        $data['name'] = $customer->name;
        $data['position'] = $position;
        $data['amount'] = $amount_payment;
        $data['payment_date'] = date('Y-m-d');
        $data['agent'] = $agent_data->name;;
        Mail::to($agent_data->email)->send(new PaymentMail($data));

        return response()->json([
            'success' => 'You add payment success'
        ]);
    }
}
