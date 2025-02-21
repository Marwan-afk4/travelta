<?php

namespace App\Http\Controllers\Api\Agent\accounting\general_ledger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Accounting\Ledger\AgentPaymentResource;
use App\Http\Resources\Accounting\Ledger\BookingEngineResource;
use App\Http\Resources\Accounting\Ledger\BookingResource;
use App\Http\Resources\Accounting\Ledger\OwnerResource;
use App\Http\Resources\Accounting\Ledger\RevenueResource;
use App\Http\Resources\Accounting\Ledger\ExpensesResource;

use App\Models\Revenue;
use App\Models\Expense;
use App\Models\AgentPayment;
use App\Models\BookingPayment;
use App\Models\BookingengineList;
use App\Models\OwnerTransaction;

class GeneralLedgerController extends Controller
{
    public function __construct(
        private Revenue $revenues,
        private Expense $expenses,
        private AgentPayment $agent_payments,
        private BookingPayment $booking_payment,
        private BookingengineList $booking_engine,
        private OwnerTransaction $owner_transactions,
    ){}

    public function view(Request $request){
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
        
        $revenues = $this->revenues
        ->with('category', 'financial', 'currency')
        ->where($role, $agent_id)
        ->get();
        $expenses = $this->expenses
        ->with('category', 'financial', 'currency')
        ->where($role, $agent_id)
        ->get();
        $agent_payments = $this->agent_payments
        ->with('financial', 'currency', 'manuel', 'supplier')
        ->where($role, $agent_id)
        ->get();
        $owner_transactions = $this->owner_transactions
        ->with('financial', 'currency', 'owner')
        ->where($role, $agent_id)
        ->get();
        $booking_engine = $this->booking_engine
        ->with('currency')
        ->where($role, $agent_id)
        ->get();
        $manuel_booking = $this->booking_payment 
        ->with(['manuel_booking' => function($query){
            $query->with('currency', 'service', 
            'hotel', 'visa', 'tour.hotel', 'flight', 'bus');
        }, 'financial'])
        ->where($role, $agent_id)
        ->get();

        $revenues = RevenueResource::collection($revenues);
        $expenses = ExpensesResource::collection($expenses);
        $agent_payments = AgentPaymentResource::collection($agent_payments);
        $owner_transactions = OwnerResource::collection($owner_transactions);
        $booking_engine = BookingEngineResource::collection($booking_engine);
        $manuel_booking = BookingResource::collection($manuel_booking);

        return response()->json([
            'revenues' => $revenues,
            'expenses' => $expenses,
            'agent_payments' => $agent_payments,
            'owner_transactions' => $owner_transactions,
            'booking_engine' => $booking_engine,
            'manuel_booking' => $manuel_booking,
        ]);
    }
}
