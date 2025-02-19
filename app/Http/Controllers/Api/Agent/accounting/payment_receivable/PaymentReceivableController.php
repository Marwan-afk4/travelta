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
        ->with(['payments'])
        ->where($role, $agent_id)
        ->get();
        $payments = PaymentReceivableResource::collection($payments);

        return response()->json([
            'payments' => $payments
        ]);
    }
}
