<?php

namespace App\Http\Controllers\Api\Agent\accounting\booking_payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBookingResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;

use App\Models\ManuelBooking;
use App\Models\FinantiolAcounting;
use App\Models\BookingPayment;
use App\Models\PaymentsCart;
use App\Models\Agent;
use App\Models\AffilateAgent;

class BookingPaymentController extends Controller
{
    public function __construct(private ManuelBooking $manuel_bookings,
    private FinantiolAcounting $financial_accounting, private BookingPayment $booking_payment,
    private PaymentsCart $payment_cart, private Agent $agent, private AffilateAgent $affilate_agent){}

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
        'payments.financial' => function($query){
            $query->select('name');
        }])
        ->where('code', $request->code )
        ->first();
        $data = collect([]);
        if (!empty($booking)) {
            $data['id'] = $booking->id;
            $data['to_client'] = $booking->to_client->name;
            $data['code'] = $booking->code;
            $data['to_phone'] = $booking->to_client->phones[0] ?? $booking->to_client->phones ?? $booking->to_client->phone;
            $data['to_email'] = $booking->to_client->emails[0] ?? $booking->to_client->emails ?? $booking->to_client->email;
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
        $booking_payment = $this->booking_payment
        ->where('id', $id)
        ->with('financial')
        ->first();
        $client = [];
        $manuel_booking = $booking_payment->manuel_booking;
        if (!empty($manuel_booking->to_supplier_id)) {
            $manuel_booking = $manuel_booking->to_client;
            $client['name'] = $manuel_booking->name;
            $client['phone'] = $manuel_booking->phones[0] ?? $manuel_booking->phones;
            $client['email'] = $manuel_booking->emails[0] ?? $manuel_booking->emails;
        }
        else{
            $manuel_booking = $manuel_booking->to_client;
            $client['name'] = $manuel_booking->name;
            $client['phone'] = $manuel_booking->phone;
            $client['email'] = $manuel_booking->email;
        }
        $booking_payment->makeHidden('manuel_booking');

        return response()->json([
            'booking_payment' => $booking_payment,
            'client' => $client
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
            $booking_payment = $this->booking_payment
            ->create([
                'manuel_booking_id' => $request->manuel_booking_id,
                'date' => date('Y-m-d'),
                'amount' => $item->amount,
                'financial_id' => $item->financial_accounting_id,
                'code' => $code,
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
        if (empty($manuel_booking->to_customer_id )) {
       
            $customer = $manuel_booking->to_customer;
            $position = 'Customer';
        }
        else{
            $customer = $manuel_booking->to_supplier;
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
