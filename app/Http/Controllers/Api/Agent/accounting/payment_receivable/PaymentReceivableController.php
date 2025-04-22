<?php

namespace App\Http\Controllers\Api\Agent\accounting\payment_receivable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Accounting\PaymentReceivableResource;

use App\Models\ManuelBooking;

class PaymentReceivableController extends Controller
{
    public function __construct(private ManuelBooking $manuel_booking){}

    public function view(Request $request){
        // /agent/accounting/payment_receivable
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

        $payments = $this->manuel_booking
        ->with(['payments', 'payments_cart'])
        ->where($role, $agent_id)
        ->get();
        $payments = PaymentReceivableResource::collection($payments);
        $total_balance = collect($payments->toArray(request()))->groupBy('currency_id')
        ->map(function($item){
            return [
                'currency' => $item[0]['currency'], 
                'over_due' => $item->sum('total_price'),
            ];
        })->values(); 
        $total_over_due = collect($payments->toArray(request()))->groupBy('currency_id')
        ->map(function($item){
            return [
                'currency' => $item[0]['currency'],
                'over_due' => $item->sum('over_due'),
            ];
        })->values();
        $total_paid = collect($payments->toArray(request()))->groupBy('currency_id')
        ->map(function($item){
            return [
                'currency' => $item[0]['currency'], 
                'over_due' => $item->sum('paid'),
            ];
        })->values(); 

        return response()->json([
            'payments' => $payments,
            'total_balance' => $total_balance,
            'total_over_due' => $total_over_due,
            'total_paid' => $total_paid,
        ]);
    }

    public function filter(Request $request){
        // /agent/accounting/payment_receivable/filter
        // Keys
        // booking_from, booking_to, due_from, due_to
        $validation = Validator::make($request->all(), [
            'booking_from' => 'nullable|date',
            'booking_to' => 'nullable|date',
            'due_from' => 'nullable|date',
            'due_to' => 'nullable|date',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
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

        $payments = $this->manuel_booking
        ->with(['payments', 'payments_cart'])
        ->where($role, $agent_id)
        ->get();
        $payments = PaymentReceivableResource::collection($payments); 
        if ($request->booking_from) {
            $payments = collect($payments->toArray(request()))
            ->where('created', '>=', $request->booking_from);
        }
        if ($request->booking_to) {
            $payments = collect($payments->toArray(request()))
            ->where('created', '<=', $request->booking_to);
        }
        if ($request->due_from) {
            $payments = collect($payments->toArray(request()))
            ->where('due_date', '>=', $request->due_from); 
        }
        if ($request->due_to) {
            $payments = collect($payments->toArray(request()))
            ->where('due_date', '<=', $request->due_to); 
        }

        return response()->json([
            'payments' => $payments->values(),
        ]);
    }
}
