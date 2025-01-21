<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingEngine extends Controller
{

    public function bookroom(Request $request){
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required',
            'check_out' => 'required',
            'quantity' => 'required',
        ]);
        $roomId = $validated['room_id'];
        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];
        $quantity = $validated['quantity'];

        $overlappingAvalibility = RoomAvailability::where('room_id', $roomId)
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->whereBetween('from', [$checkIn, $checkOut])
                ->orWhereBetween('to', [$checkIn, $checkOut])
                ->orWhereRaw('? BETWEEN `from` AND `to`', [$checkIn])
                ->orWhereRaw('? BETWEEN `from` AND `to`', [$checkOut]);
        })->orderBy('from', 'asc')
        ->get();

        $remainingRoomsToBook = $quantity;

        foreach ($overlappingAvalibility as $avalibility) {
            $overlapstart = max($checkIn, $avalibility->from);
            $overlapend = min($checkOut, $avalibility->to);
            $overlapDays = (new Carbon($overlapend))->diffInDays(new Carbon($overlapstart))+1;

            if($remainingRoomsToBook > 0 && $avalibility->quantity >0){
                
            }
        }

    }
}
