<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use App\Models\Plan;
use Illuminate\Http\Request;

class PaymentController extends Controller
{


    public function getPyamnts(){
        $payments = ManualPayment::where('status','pending')->get();
        $data = $payments->map(function ($payment) {
            return [
                'payment_id' => $payment->id,
                'affilate_agent_id' => $payment->affilate_agent_id ?? null,
                'affilate_agent_name' => $payment->affilate_agent->f_name . ' ' . $payment->affilate_agent->l_name ?? null,
                'affilate_agent_email' => $payment->affilate_agent->email ?? null,
                'affilate_agent_phone' => $payment->affilate_agent->phone ?? null,
                'agent_id' => $payment->agency_id ?? null,
                'agent_name' => $payment->agent->name ?? null,
                'agent_email' => $payment->agent->email ?? null,
                'agent_phone' => $payment->agent->phone ?? null,
                'plan_id' => $payment->plan_id ,
                'plan_name' => $payment->plan->name ,
                'plan_price' => $payment->plan->price_after_discount ,
                'start_date'=> $payment->start_date ,
                'end_date'=> $payment->end_date ,
                'receipt'=> $payment->receipt
            ];
        });
        return response()->json(['payments' => $data]);
    }

    public function approvePayment($id){
        $payment = ManualPayment::find($id);
        $payment->status = 'approved';
        $payment->save();
        return response()->json([
            'message'=>'payment approved successfully'
        ]);
    }

    public function rejectPayment($id){
        $payment = ManualPayment::find($id);
        $payment->status = 'rejected';
        $payment->save();
        return response()->json([
            'message'=>'payment rejected successfully'
        ]);
    }
}
