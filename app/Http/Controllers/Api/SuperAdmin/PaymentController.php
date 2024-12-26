<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    public function getPyamnts()
    {
        $payments = ManualPayment::where('status', 'pending')->get();
        $data = $payments->map(function ($payment) {
            return [
                'payment_id' => $payment->id,
                'payment_method_id' => $payment->payment_method_id,
                'payment_method_name' => $payment->paymentMethod->name,
                'affilate_agent_id' => $payment->affilate_agent_id ?? null,
                'affilate_agent_name' => $payment->affilate_agent->f_name . ' ' . $payment->affilate_agent->l_name ?? null,
                'affilate_agent_email' => $payment->affilate_agent->email ?? null,
                'affilate_agent_phone' => $payment->affilate_agent->phone ?? null,
                'agent_id' => $payment->agency_id ?? null,
                'agent_name' => $payment->agent->name ?? null,
                'agent_email' => $payment->agent->email ?? null,
                'agent_phone' => $payment->agent->phone ?? null,
                'plan_id' => $payment->plan_id,
                'plan_name' => $payment->plan->name,
                'plan_price' => $payment->plan->price_after_discount,
                'start_date' => $payment->start_date,
                'end_date' => $payment->end_date,
                'receipt' => $payment->receipt
            ];
        });
        return response()->json(['payments' => $data]);
    }

    public function rejectPayment($id)
    {
        $payment = ManualPayment::find($id);
        $payment->status = 'rejected';
        $payment->save();
        return response()->json([
            'message' => 'payment rejected successfully'
        ]);
    }

    public function makePayment(Request $request)
    {
        $authuser = Auth::user();

        $validation = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'plan_id' => 'required|exists:plans,id',
            'receipt' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 401);
        }

        $plan = Plan::find($request->plan_id);

        $affiliateAgentId = null;
        $agencyId = null;

        if ($authuser->role === 'agent' || $authuser->role === 'supplier') {
            $agencyId = $authuser->id;
        } elseif ($authuser->role === 'freelancer' || $authuser->role === 'affiliate') {
            $affiliateAgentId = $authuser->id;
        }


        $payment = ManualPayment::create([
            'payment_method_id' => $request->payment_method_id,
            'affiliate_agent_id' => $affiliateAgentId??null,
            'agency_id' => $agencyId??null,
            'plan_id' => $request->plan_id,
            'receipt' => $request->receipt,
            'status' => 'pending',
            'amount' => $plan->price_after_discount,
            'start_date' => null,
            'end_date' => null
        ]);

        return response()->json([
            'message' => 'Payment added successfully',
            'payment' => $payment
        ]);
    }



    public function acceptPayment($plan_id, $id)
    {
        $plan = Plan::findOrFail($plan_id);
        $payment = ManualPayment::find($id);
        $payment->status = 'approved';
        $payment->start_date = now();
        $payment->end_date = now()->addDays($plan->period_in_days);
        $payment->save();
        return response()->json(['message' => 'payment accepted successfually']);
    }
}
