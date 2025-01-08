<?php

namespace App\Http\Controllers\Api\Agent\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Wallet;
use App\Models\ChargeWallet;

class WalletController extends Controller
{
    public function __construct(private Wallet $wallet, private ChargeWallet $charge_wallet){}
    protected $chargeWallet = [
        'wallet_id',
        'payment_method_id',
        'amount'
    ]; 

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
            $wallet = $this->wallet 
            ->where('affilate_id', $agent_id)
            ->with('currancy')
            ->get();
        } 
        else {
            $wallet = $this->wallet 
            ->where('agent_id', $agent_id)
            ->with('currancy')
            ->get();
        }

        return response()->json([
            'wallets' => $wallet,
        ]);
    }

    public function add(Request $request){
        $validation = Validator::make($request->all(), [
            'currancy_id' => 'required|exists:currancies,id',
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
            $currancy_wallet = $this->wallet 
            ->where('affilate_id', $agent_id)
            ->where('currancy_id', $request->currancy_id)
            ->first();
            if (!empty($currancy_wallet)) {
                return response()->json([
                    'errors' => 'Agency Already has this Currency!'
                ], 400);
            }
            $wallet = $this->wallet
            ->create([
                'affilate_id' => $agent_id,
                'currancy_id' => $request->currancy_id,
            ]);
        } 
        else { 
            $currancy_wallet = $this->wallet 
            ->where('agent_id', $agent_id)
            ->where('currancy_id', $request->currancy_id)
            ->first();
            if (!empty($currancy_wallet)) {
                return response()->json([
                    'errors' => 'Agency Already has this Currency!'
                ], 400);
            }
            $wallet = $this->wallet
            ->create([
                'agent_id' => $agent_id,
                'currancy_id' => $request->currancy_id,
            ]);
        }

        return response()->json([
            'success' => $wallet,
        ]);
    }

    public function delete(Request $request, $id){
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
            $wallet = $this->wallet 
            ->where('affilate_id', $agent_id)
            ->where('id', $id)
            ->where('amount', '<=', 0)
            ->first();
            if (empty($wallet)) {
                return response()->json([
                    'errors' => 'Wallet is not empty!'
                ], 400);
            }
            $wallet->delete();
        } 
        else { 
            $wallet = $this->wallet 
            ->where('agent_id', $agent_id)
            ->where('id', $id)
            ->where('amount', '<=', 0)
            ->first();
            if (empty($wallet)) {
                return response()->json([
                    'errors' => 'Wallet is not empty!'
                ], 400);
            }
            $wallet->delete();
        }

        return response()->json([
            'success' => 'You delete Wallet success'
        ], 200);
    }

    public function charge(Request $request){
        $validation = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:wallets,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $walletRequest = $request->only('wallet_id', 'payment_method_id', 'amount');
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
            $walletRequest['affilate_id'] = $agent_id;
        } 
        else {
            $walletRequest['agent_id'] = $agent_id;
        }
        $charge_wallet = $this->charge_wallet
        ->create($walletRequest);
    }
}
