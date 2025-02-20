<?php

namespace App\Http\Controllers\Api\Agent\accounting\payment_receivable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $total_balance = $payments->sum('total_price');
        $total_over_due = 0;
        $total_paid = 0;
        foreach ($payments as $item) {
            $total_over_due += $item->over_due;
        }

        return response()->json([
            'payments' => $payments,
            'total_balance' => $total_balance,
            'total_over_due' => $total_over_due,
        ]);
    }
}
