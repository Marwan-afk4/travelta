<?php

namespace App\Http\Controllers\Api\Agent\accounting\supplier_payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\ManuelBooking; 
use App\Models\PaymentsCart;
use App\Models\AgentPayment;
use App\Models\BookingPayment;

class SupplierPaymentController extends Controller
{
    public function __construct(
        private ManuelBooking $manuel_booking,  
        private PaymentsCart $payment_cart,
        private AgentPayment $agent_payment, 
        private BookingPayment $booking_payment,
    ){}

    public function transactions(Request $request ,$id){
        // agent/accounting/transactions/{id}
        // if invoice booking_payment => /accounting/booking/invoice/{id}
        // if invoice agent_payment
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
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $due_supplier = $this->payment_cart
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->where('status', 'approve')
        ->get();
        $due_from_supplier = $due_supplier->sum('due_payment');
        $due_from_agent = $this->manuel_booking
        ->where('from_supplier_id', $id)
        ->sum('cost');
        $due_from_agent -= $this->agent_payment
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->sum('amount');
        $debt = 0;
        $credit = 0;
        if ($due_from_supplier > $due_from_agent) {
            $credit = $due_from_supplier - $due_from_agent;
        } else {
            $debt = $due_from_agent - $due_from_supplier;
        }
        $agent_payment = $this->agent_payment
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->get();
        $booking_payment = $this->booking_payment
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->get();
        
        return response()->json([
                'total_credit' => $credit,
                'total_debt' => $debt,
                'agent_payment' => $agent_payment,
                'booking_payment' => $booking_payment,
        ]);
    }

    public function add_payment(Request $request){
        // agent/accounting/transactions_payment
        // Keys
        // amount, date, supplier_id
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:supplier_agents,id',
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
            $agent_type = 'affilate_id';
        } 
        else {
            $agent_type = 'agent_id';
        }
        
        $amount =  $request->amount;
        $code = Str::random(8);
        $agent_payment = $this->agent_payment
        ->where('code', $code)
        ->first();
        while (!empty($agent_payment)) {
            $code = Str::random(8);
            $agent_payment = $this->agent_payment
            ->where('code', $code)
            ->first();
        }
        
        $this->agent_payment
        ->create([
            $agent_type => $agent_id,
            'supplier_id' => $request->supplier_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'code' => $code,
        ]);

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
