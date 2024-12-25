<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{


    public function getPyamnts(){
        $payments = ManualPayment::where('status','pending')->get();
        $data = [
            'pending payments'=>$payments
        ];
        return response()->json($data);
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
