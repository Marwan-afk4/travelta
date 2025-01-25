<?php

namespace App\Http\Controllers\Api\Agent\accounting\booking_payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBookingResource;
use Illuminate\Support\Facades\Validator;

use App\Models\ManuelBooking;
use App\Models\FinantiolAcounting;

class BookingPaymentController extends Controller
{
    public function __construct(private ManuelBooking $manuel_bookings,
    private FinantiolAcounting $financial_accounting){}

    public function search(Request $request){
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
        ->with('hotel', 'bus', 'flight', 'tour', 'visa', 'adults', 'children')
        ->where('code', $request->code )
        ->first();
        $data = collect([]);
        if (!empty($booking)) {
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
        ->get();

        return response()->json([
            'booking' => $data,
            'financial_accounting' => $financial_accounting,
            'currency' => $booking->currency->name,
            'total' => $booking->manuel_cart[0]?->total ?? $booking->total_price,
            'paid' => ($booking->manuel_cart->where('status', 'approve')[0]?->payment ?? 0) + ($booking->payments_cart->where('status', 'approve')->sum('payment')),
        ]);
    }
}
