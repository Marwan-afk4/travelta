<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{


    public function getPaymentMethods(){
        $paymentMethod = PaymentMethod::all();
        $data = [
            'paymentMethods' => $paymentMethod
        ];
        return response()->json($data);
    }

    public function addPaymentMethod(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'nullable',
            'description' => 'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $paymenMethod = PaymentMethod::create([
            'name' => $request->name,
            'image' => $request->image,
            'description' => $request->description,
            'status' => 'active'
        ]);
        return response()->json([
            'message'=>'payment method added successfully'
        ]);
    }

    public function deletePaymentMethod($id){
        $paymentMethod = PaymentMethod::find($id);
        $paymentMethod->delete();
        return response()->json([
            'message'=>'payment method deleted successfully'
        ]);
    }
}
