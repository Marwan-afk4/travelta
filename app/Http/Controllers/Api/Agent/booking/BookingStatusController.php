<?php

namespace App\Http\Controllers\Api\Agent\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\ManuelBooking;
use App\Models\PaymentsCart;
use App\Models\BookingengineList;
use App\Models\BookTourengine;
use App\Models\OperationBookingConfirmed;
use App\Models\OperationBookingVouchered;
use App\Models\OperationBookingCanceled;

class BookingStatusController extends Controller
{
    public function __construct(private ManuelBooking $manuel_booking,
    private OperationBookingConfirmed $operation_confirmed, 
    private OperationBookingVouchered $operation_vouchered,
    private OperationBookingCanceled $operation_canceled,
    private PaymentsCart $payments,
    private BookingengineList $engine_booking,
    private BookTourengine $engine_tour_booking){}
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
        $engine_booking = $this->engine_booking
        ->where('id', $id)
        ->first();
        $engine_booking->update([
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
        $engine_booking = $this->engine_booking
        ->where('id', $id)
        ->first();
        $engine_booking->update([
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
        $engine_booking = $this->engine_booking
        ->where('id', $id)
        ->first();
        $engine_booking->update([
            'status' => 'canceled',
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }
    // _____________________________________________________________________________
    public function engine_tour_confirmed(Request $request, $id){
        // agent/booking/engine_tour_confirmed/{id}
        $engine_tour_booking = 
        $this->engine_tour_booking
        ->where('id', $id)
        ->first();
        $engine_tour_booking->update([
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function engine_tour_vouchered(Request $request, $id){
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
        $voucheredRequest['engine_tour_id'] = $id;
        $operation_vouchered = $this->operation_vouchered
        ->create($voucheredRequest);
        $engine_tour_booking = $this->engine_tour_booking
        ->where('id', $id)
        ->first();
        $engine_tour_booking->update([
            'status' => 'vouchered'
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }

    public function engine_tour_canceled(Request $request, $id){
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
            'engine_tour_id' => $id,
            'cancelation_reason' => $request->cancelation_reason,
        ]);
        $engine_tour_booking = $this->engine_tour_booking
        ->where('id', $id)
        ->first();
        $engine_tour_booking->update([
            'status' => 'canceled',
        ]);

        return response()->json([
            'success' => 'you update data success'
        ]);
    }
}
