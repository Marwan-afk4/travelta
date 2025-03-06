<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ManuelBooking;
use App\Models\PaymentsCart;
use App\Models\OperationBookingConfirmed;
use App\Models\OperationBookingVouchered;
use App\Models\OperationBookingCanceled;

class BookingStatusController extends Controller
{
    public function __construct(private ManuelBooking $manuel_booking,
    private OperationBookingConfirmed $operation_confirmed, 
    private OperationBookingVouchered $operation_vouchered,
    private OperationBookingCanceled $operation_canceled,
    private PaymentsCart $payments){}
    protected $voucheredRequest = [
        'totally_paid',
        'confirmation_num',
        'name',
        'phone',
        'email',
    ];

    public function confirmed(Request $request, $id){
        // agent/booking/confirmed/{id}

        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function vouchered(Request $request, $id){
        // agent/booking/vouchered/{id}
        // Keys
        // totally_paid, confirmation_num, name, phone, email
        $validation = Validator::make($request->all(), [
            'totally_paid' => 'required|boolean',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $payments = $this->payments
        ->where('manuel_booking_id', $id)
        ->get();
        $due_payment = $payments->sum('due_payment');
        if ($due_payment > 0) {
            return response()->json([
                'errors' => 'All amount must be paid'
            ], 400);
        }
        $voucheredRequest = $request->only($this->voucheredRequest);
        $voucheredRequest['manuel_booking_id'] = $id;
        $operation_vouchered = $this->operation_vouchered
        ->create($voucheredRequest);
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'vouchered'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function canceled(Request $request, $id){
        // agent/booking/canceled/{id}
        // Keys
        // cancelation_reason
        $validation = Validator::make($request->all(), [
            'cancelation_reason' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $this->operation_canceled
        ->create([
            'manuel_booking_id' => $id,
            'cancelation_reason' => $request->cancelation_reason,
        ]);
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'canceled',
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function engine_confirmed(Request $request, $id){
        // agent/booking/engine_confirmed/{id}
        // Keys
        // comfirmed, deposits[{deposit, date}]
        $validation = Validator::make($request->all(), [
            'comfirmed' => 'required|boolean',
            'deposits' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $deposits = is_string($request->deposits) ? $request->deposits
        : json_encode($request->deposits);
        $operation_confirmed = $this->operation_confirmed
        ->create([
            'comfirmed' => $request->comfirmed,
            'deposits' => $deposits,
            'booking_engine_id' => $id,
        ]);
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function engine_vouchered(Request $request, $id){
        // agent/booking/vouchered/{id}
        // Keys
        // totally_paid, confirmation_num, name, phone, email
        $validation = Validator::make($request->all(), [
            'totally_paid' => 'required|boolean',
            'confirmation_num' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $voucheredRequest = $request->only($this->voucheredRequest);
        $voucheredRequest['booking_engine_id'] = $id;
        $operation_vouchered = $this->operation_vouchered
        ->create($voucheredRequest);
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'vouchered'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function engine_canceled(Request $request, $id){
        // agent/booking/canceled/{id}
        // Keys
        // cancelation_reason
        $validation = Validator::make($request->all(), [
            'cancelation_reason' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $this->operation_canceled
        ->create([
            'booking_engine_id' => $id,
            'cancelation_reason' => $request->cancelation_reason,
        ]);
        $this->manuel_booking
        ->where('id', $id)
        ->update([
            'status' => 'canceled',
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }
}
