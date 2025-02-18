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
use App\Models\AgentPayable;

class SupplierPaymentController extends Controller
{
    public function __construct(
        private ManuelBooking $manuel_booking,  
        private PaymentsCart $payment_cart,
        private AgentPayment $agent_payment, 
        private BookingPayment $booking_payment,
        private AgentPayable $agent_payable,
    ){}

    // public function transactions(Request $request ,$id){
    //     // agent/accounting/transactions/{id}
    //     // if invoice booking_payment => /accounting/booking/invoice/{id}
    //     // if invoice agent_payment
    //     if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
    //         $agent_id = $request->user()->affilate_id;
    //     }
    //     elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
    //         $agent_id = $request->user()->agent_id;
    //     }
    //     else{
    //         $agent_id = $request->user()->id;
    //     }
    //     if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
    //         $agent_type = 'affilate_id';
    //     }
    //     else {
    //         $agent_type = 'agent_id';
    //     }

    //     $due_supplier = $this->payment_cart
    //     ->where($agent_type, $agent_id)
    //     ->where('supplier_id', $id)
    //     ->where('status', 'approve')
    //     ->get();
    //     $due_from_supplier = $due_supplier->sum('due_payment');
    //     $due_from_agent = $this->manuel_booking
    //     ->where('from_supplier_id', $id)
    //     ->sum('cost');
    //     $due_from_agent -= $this->agent_payment
    //     ->where($agent_type, $agent_id)
    //     ->where('supplier_id', $id)
    //     ->sum('amount');
    //     $debt = 0;
    //     $credit = 0;
    //     if ($due_from_supplier > $due_from_agent) {
    //         $credit = $due_from_supplier - $due_from_agent;
    //     } else {
    //         $debt = $due_from_agent - $due_from_supplier;
    //     }
    //     $agent_payment = $this->agent_payment
    //     ->where($agent_type, $agent_id)
    //     ->where('supplier_id', $id)
    //     ->get();
    //     $booking_payment = $this->booking_payment
    //     ->where($agent_type, $agent_id)
    //     ->where('supplier_id', $id)
    //     ->get();
        
    //     return response()->json([
    //             'total_credit' => $credit,
    //             'total_debt' => $debt,
    //             'agent_payment' => $agent_payment,
    //             'booking_payment' => $booking_payment,
    //     ]);
    // }
    

    public function paid_to_suppliers(Request $request){
        // agent/accounting/paid_to_suppliers
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

        $agent_payment = $this->agent_payment
        ->where($agent_type, $agent_id) 
        ->with(['supplier:id,agent', 'manuel:id,code,cost', 
        'financial:id,name', 'currency:id,name'])
        ->get();
        
        return response()->json([
            'agent_payment' => $agent_payment, 
        ]);
    }

    public function paid_to_suppliers_filter(Request $request){
        // agent/accounting/paid_to_suppliers_filter
       // keys
       // from, to
        $validation = Validator::make($request->all(), [
            'from' => 'nullable|date',
            'to' => 'nullable|date'
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

        $agent_payment = $this->agent_payment
        ->where($agent_type, $agent_id)
        ->with(['supplier:id,agent', 'manuel:id,code,cost', 
        'financial:id,name', 'currency:id,name'])
        ->get();
        if ($request->from) {
            $agent_payment = $agent_payment->where('date', '>=', $request->from);
        }
        if ($request->to) {
            $agent_payment = $agent_payment->where('date', '<=', $request->to);
        }
        
        return response()->json([
            'agent_payment' => $agent_payment->values(), 
        ]);
    }

    public function payable_to_suppliers(Request $request){
        // agent/accounting/payable_to_suppliers
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

        $agent_payable = $this->agent_payable
        ->where($agent_type, $agent_id)
        ->with(['supplier:id,agent', 'currency:id,name'])
        ->get();
        
        return response()->json([
            'agent_payable' => $agent_payable, 
        ]);
    }

    public function payable_to_suppliers_filter(Request $request){
        // agent/accounting/payable_to_suppliers_filter 
        // Keys
        // payable_from, payable_to, due_from, due_to
        $validation = Validator::make($request->all(), [
            'payable_from' => 'nullable|date',
            'payable_to' => 'nullable|date',
            'due_from' => 'nullable|date',
            'due_to' => 'nullable|date',
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

        $agent_payable = $this->agent_payable
        ->where($agent_type, $agent_id)
        ->with(['supplier:id,agent', 'currency:id,name'])
        ->get(); 
        if ($request->payable_from) {
            $agent_payable = $agent_payable->where('manuel_date', '>=', $request->payable_from);
        }
        if ($request->payable_to) {
            $agent_payable = $agent_payable->where('manuel_date', '<=', $request->payable_to);
        }
        if ($request->due_from) {
            $agent_payable = $agent_payable->where('due_date', '>=', $request->due_from);
        }
        if ($request->due_to) {
            $agent_payable = $agent_payable->where('due_date', '<=', $request->due_to);
        }
        
        return response()->json([
            'agent_payable' => $agent_payable->values(), 
        ]);
    }

    public function due_to_suppliers(Request $request){
        // agent/accounting/due_to_suppliers
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

        $agent_payable = $this->agent_payable
        ->where($agent_type, $agent_id)
        ->where('due_date', '<=', date('Y-m-d'))
        ->with(['supplier:id,agent', 'currency:id,name'])
        ->get();
        
        return response()->json([
            'agent_payable' => $agent_payable, 
        ]);
    }

    public function due_to_suppliers_filter(Request $request){
        // agent/accounting/due_to_suppliers_filter
        // Keys
        // payable_from, payable_to, due_from, due_to
        $validation = Validator::make($request->all(), [
            'payable_from' => 'nullable|date',
            'payable_to' => 'nullable|date',
            'due_from' => 'nullable|date',
            'due_to' => 'nullable|date',
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

        $agent_payable = $this->agent_payable
        ->where($agent_type, $agent_id)
        ->where('due_date', '<=', date('Y-m-d'))
        ->with(['supplier:id,agent', 'currency:id,name'])
        ->get(); 
        if ($request->payable_from) {
            $agent_payable = $agent_payable->where('manuel_date', '>=', $request->payable_from);
        }
        if ($request->payable_to) {
            $agent_payable = $agent_payable->where('manuel_date', '<=', $request->payable_to);
        }
        if ($request->due_from) {
            $agent_payable = $agent_payable->where('due_date', '>=', $request->due_from);
        }
        if ($request->due_to) {
            $agent_payable = $agent_payable->where('due_date', '<=', $request->due_to);
        }
        
        return response()->json([
            'agent_payable' => $agent_payable->values(), 
        ]);
    }

    public function add_payment(Request $request){
        // agent/accounting/transactions_payment
        // Keys
        // amount, date, supplier_id, manuel_booking_id, financial_id, 
        // currency_id
        $validation = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:supplier_agents,id',
            'manuel_booking_id' => 'required|exists:manuel_bookings,id',
            'financial_id' => 'required|exists:finantiol_acountings,id',
            'currency_id' => 'required|exists:currency_agents,id',
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
            'manuel_booking_id' => $request->manuel_booking_id,
            'financial_id' => $request->financial_id,
            'currency_id' => $request->currency_id
        ]);
        $agent_payable = $this->agent_payable
        ->where('manuel_booking_id', $request->manuel_booking_id)
        ->first();
        if (!empty($agent_payable)) {
            $agent_payable->paid += $request->amount;
            $agent_payable->save();
        }
        $this->agent_payable
        ->whereColumn('paid', '>=', 'payable')
        ->delete();

        return response()->json([
            'success' => 'You add data success'
        ]);
    }
}
