<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChargeWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RechargeWalletController extends Controller
{


    public function getPendingWallets(){
        $pencdingrechareWallet = ChargeWallet::where('status', 'pending')->get();
        foreach ($pencdingrechareWallet as $wallet) {
            $wallet->image = url ('storage/' . $wallet->image);
        }
        $data = [
            'pending_wallets' => $pencdingrechareWallet
        ];
        return response()->json($data);
    }

    public function approveRechargeWallet($id){
        $rechargeWallet = ChargeWallet::find($id);
        $rechargeWallet->update([
            'status' => 'approve'
        ]);
        return response()->json([
            'message' => 'the wallet recharge approved successfully'
        ]);
    }

    public function rejectRechargeWallet(Request $request,$id){
        $rechargeWallet = ChargeWallet::find($id);
        $validation = Validator::make($request->all(), [
            'rejected_reason' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->errors()->first()
            ]);
        }
        $rechargeWallet->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason
        ]);
        return response()->json([
            'message' => 'the wallet recharge rejected successfully'
        ]);
    }
}
