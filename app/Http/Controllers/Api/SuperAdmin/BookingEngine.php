<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BookingEngine as ModelsBookingEngine;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingEngine extends Controller
{


    public function bookroom(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in',
            'quantity' => 'required|integer|min:1',
        ]);

        $roomId = $validated['room_id'];
        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];
        $quantity = $validated['quantity'];

        DB::beginTransaction();
        try {
            $overlappingAvailabilities = RoomAvailability::where('room_id', $roomId)
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('from', [$checkIn, $checkOut])
                        ->orWhereBetween('to', [$checkIn, $checkOut])
                        ->orWhereRaw('? BETWEEN `from` AND `to`', [$checkIn])
                        ->orWhereRaw('? BETWEEN `from` AND `to`', [$checkOut]);
                })
                ->orderBy('from', 'asc')
                ->lockForUpdate() // Prevent race conditions
                ->get();

            $remainingRoomsToBook = $quantity;

            foreach ($overlappingAvailabilities as $availability) {
                // Calculate the overlapping period
                $overlapStart = max($checkIn, $availability->from);
                $overlapEnd = min($checkOut, $availability->to);

                $availableRooms = $availability->quantity;
                if ($availableRooms > 0) {
                    $roomsToDeduct = min($remainingRoomsToBook, $availableRooms);

                    // Deduct from availability
                    $availability->quantity -= $roomsToDeduct;

                    // If the availability is completely consumed, split the range
                    if ($availability->quantity == 0) {
                        if ($overlapStart > $availability->from) {
                            RoomAvailability::create([
                                'room_id' => $roomId,
                                'from' => $availability->from,
                                'to' => Carbon::parse($overlapStart)->subDay(),
                                'quantity' => $roomsToDeduct,
                            ]);
                        }

                        if ($overlapEnd < $availability->to) {
                            RoomAvailability::create([
                                'room_id' => $roomId,
                                'from' => Carbon::parse($overlapEnd)->addDay(),
                                'to' => $availability->to,
                                'quantity' => $roomsToDeduct,
                            ]);
                        }

                        $availability->delete(); // Remove the exhausted record
                    } else {
                        $availability->save();
                    }

                    $remainingRoomsToBook -= $roomsToDeduct;

                    // Stop if we have fulfilled the booking
                    if ($remainingRoomsToBook <= 0) {
                        break;
                    }
                }
            }

            if ($remainingRoomsToBook > 0) {
                // Rollback and return error if not enough rooms are available
                DB::rollBack();
                return response()->json(['error' => 'Not enough rooms available'], 400);
            }

            // Save the booking
            $booking = ModelsBookingEngine::create([
                'room_id' => $roomId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'quantity' => $quantity,
            ]);

            DB::commit();
            return response()->json(['success' => 'Room booked successfully', 'booking' => $booking], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while booking the room', 'message' => $e->getMessage()], 500);
        }
    }
}
