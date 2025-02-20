<?php

namespace App\Http\Controllers\Api\Agent\accounting\general_ledger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Accounting\Ledger\AgentPaymentResource;
use App\Http\Resources\Accounting\Ledger\BookingEngineResource;
use App\Http\Resources\Accounting\Ledger\BookingResource;
use App\Http\Resources\Accounting\Ledger\ExpensesResource;
use App\Http\Resources\Accounting\Ledger\OwnerResource;
use App\Http\Resources\Accounting\Ledger\RevenueResource;

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
        ->with('category', 'financial')
        ->where($role, $agent_id)
        ->get();
        $expenses = $this->expenses
        ->with('category')
        ->where($role, $agent_id)
        ->get();

        $revenues = RevenueResource::collection($revenues);

        return response()->json([
            'revenues' => $revenues,
            'expenses' => $expenses,
        ]);
    }
}
