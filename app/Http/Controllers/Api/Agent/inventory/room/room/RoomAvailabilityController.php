<?php

namespace App\Http\Controllers\Api\Agent\inventory\room\room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\api\agent\inventory\room\room\RoomAvailabilityRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

use App\Models\RoomAvailability;
use App\Models\BookingEngine;

class RoomAvailabilityController extends Controller
{
    public function __construct(private RoomAvailability $room_availability,
    private BookingEngine $booking_engine){}

    public function view(Request $request){
        $validation = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'year' => 'required|numeric',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        $room_availability = $this->room_availability
        ->where('room_id', $request->room_id)
        ->get();
        $year = $request->year;

        $startOfYear = Carbon::createFromDate($year)->startOfYear();
        $endOfYear = Carbon::createFromDate($year)->endOfYear();
        $dates = $startOfYear->toPeriod($endOfYear);
        $availability = [];

        foreach ($dates as $date) {
            $date = Carbon::parse($date)->format('Y-m-d');
            $quantity = $this->room_availability
            ->where('room_id', $request->room_id)
            ->where('from', '<=', $date)
            ->where('to', '>=', $date)
            ->sum('quantity') - 
            $this->booking_engine
            ->where('room_id', $request->room_id)
            ->where('check_in', '<=', $date)
            ->where('check_out', '>=', $date)
            ->sum('quantity');
            $availability[] = [
                'date' => $date,
                'quantity' => $quantity,
            ];
        }

        return response()->json([
            'availability' => $availability
        ]);
    }

    public function room_availability($id){
    }

    public function create(RoomAvailabilityRequest $request){
        $roomRequest = $request->validated();
        foreach ($request->rooms as $item) {
            $this->room_availability
            ->create([
                'room_id' => $request->room_id,
                'from' => $item['from'],
                'to' => $item['to'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request){
        $validation = Validator::make($request->all(), [
            'room_id' => ['required', 'exists:rooms,id'],
            'rooms.*.from' => ['required', 'date'],
            'rooms.*.to' => ['required', 'date'],
            'rooms.*.quantity' => ['required', 'numeric'],
            'add_rooms.*.from' => ['required', 'date'],
            'add_rooms.*.to' => ['required', 'date'],
            'add_rooms.*.quantity' => ['required', 'numeric'],
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        if ($request->rooms) {
            foreach ($request->rooms as $item) {
                $from = Carbon::parse($item['from']);
                $to = Carbon::parse($item['to']);
                $dates = $from->toPeriod($to);
                
                foreach ($dates as $date) {
                    $quantity = $this->room_availability
                    ->where('room_id', $request->room_id)
                    ->where('from', '<=', $date)
                    ->where('to', '>=', $date)
                    ->sum('quantity') - 
                    $this->booking_engine
                    ->where('room_id', $request->room_id)
                    ->where('check_in', '<=', $date)
                    ->where('check_out', '>=', $date)
                    ->sum('quantity') - $item['quantity'];
                    $date = Carbon::parse($date)->format('Y-m-d');
                    if ($quantity != 0) {
                        $this->booking_engine
                        ->where('room_id', $request->room_id)
                        ->create([
                            'room_id' => $request->room_id,
                            'check_in' => $date,
                            'check_out' => $date,
                            'quantity' => $quantity,
                        ]);
                    }
                }
            }
        }
        elseif ($request->add_rooms) {
            foreach ($request->add_rooms as $item) {
                $check_in = $item['from'];
                $check_out = $item['to'];
                // Delete Booking at this period
                $this->booking_engine
                ->where('room_id', $request->room_id)
                ->create([
                    'room_id' => $request->room_id,
                    'check_in' => $check_in,
                    'check_out' => $check_out,
                    'quantity' => -$item['quantity'],
                ]);
                
            }
        }

        return response()->json([
            'success' => $request->all()
        ]);
    }

    public function delete($id){
        $this->room_availability
        ->where('id', $id)
        ->delete();

        return response()->json([
            'success' => 'You delete data success'
        ]);
    }
}
