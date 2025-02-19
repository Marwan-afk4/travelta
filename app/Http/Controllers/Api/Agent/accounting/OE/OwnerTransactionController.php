<?php

namespace App\Http\Controllers\Api\Agent\accounting\OE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\api\agent\accounting\OE\TransactionRequest;

use App\Models\Owner;
use App\Models\OwnerTransaction;

class OwnerTransactionController extends Controller
{
    public function __construct(private OwnerTransaction $owner_transactions,
    private Owner $owner){}

    public function transaction(TransactionRequest $request){
        // /agent/accounting/owner/transaction
        // Keys
        // owner_id, financial_id, amount, type => [withdraw, depost]
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

        $transactions = $request->validated();
        $owner = $this->owner
        ->where('id', $request->owner_id)
        ->first();
        $transactions['currency_id'] = $owner->currency_id;
        if ($request->type == 'withdraw') {
            if ($owner->balance - $request->amount < 0) {
                return response()->json([
                    'errors' => "You Don't have {$request->amount} in your account",
                ], 400);
            }
        }
        $transactions[$role] = $agent_id;
        $this->owner_transactions
        ->create($transactions);
        if ($request->type == 'withdraw') {
            $owner->balance -= $request->amount;
        } 
        else {
            $owner->balance += $request->amount;
        }
        $owner->save();

        return response()->json([
            'success' => 'You make transaction success'
        ]);
    }
    
    public function transactions_list(Request $request){
        // /agent/accounting/owner/transactions_list
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
        $transactions = $this->owner_transactions
        ->where($role, $agent_id)
        ->with(['currency:id,name', 'owner:id,name', 'financial:id,name'])
        ->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
