<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BookingEngine as ModelsBookingEngine;
use App\Models\CustomerBookingengine;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingEngine extends Controller
{

    public function getAvailableRooms(Request $request)
{
    $validated = $request->validate([
        'check_in'  => 'required|date|before:check_out',
        'check_out' => 'required|date|after:check_in',
    ]);

    $checkIn = Carbon::parse($validated['check_in']);
    $checkOut = Carbon::parse($validated['check_out']);

    try {
        $availableRooms = [];

        // Fetch all distinct room IDs in the availability table
        $rooms = Room::with(['availability'])->get(); // Assuming you have a `Room` model with a `RoomAvailability` relationship.

        foreach ($rooms as $room) {
            $roomId = $room->id;
            $remainingQuantity = null; // Start with an unlimited quantity
            $currentDate = clone $checkIn;

            // Check availability for each day in the booking period
            while ($currentDate <= $checkOut) {
                $dailyAvailable = RoomAvailability::where('room_id', $roomId)
                    ->whereDate('from', '<=', $currentDate)
                    ->whereDate('to', '>=', $currentDate)
                    ->sum('quantity');

                $dailyBooked = ModelsBookingEngine::where('room_id', $roomId)
                    ->whereDate('check_in', '<=', $currentDate)
                    ->whereDate('check_out', '>', $currentDate)
                    ->sum('quantity');

                $dailyRemaining = $dailyAvailable - $dailyBooked;

                // If no rooms are available on any day, skip this room
                if ($dailyRemaining <= 0) {
                    $remainingQuantity = 0;
                    break;
                }

                // Track the minimum remaining quantity for the room
                $remainingQuantity = is_null($remainingQuantity)
                    ? $dailyRemaining
                    : min($remainingQuantity, $dailyRemaining);

                $currentDate = $currentDate->addDay();
            }

            // If the room has availability, add it to the result
            if ($remainingQuantity > 0) {
                $availableRooms[] = [
                    'room_id' => $roomId,
                    'available_quantity' => $remainingQuantity,
                    'room_details' => $room, // Include all room data
                ];
            }
        }

        return response()->json([
            'success' => true,
            'available_rooms' => $availableRooms,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while fetching available rooms.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



public function bookRoom(Request $request)
{
    $validator = Validator::make($request->all(), [
        'room_id'       => 'required|integer|exists:rooms,id',
        'check_in'      => 'required|date|before:check_out',
        'check_out'     => 'required|date|after:check_in',
        'quantity'      => 'required|integer|min:1',
        'customer_id'   => 'nullable|integer|exists:customers,id',
        'adults'        => 'required|integer|min:1',
        'children'      => 'nullable|integer|min:0',
        'nationality_id' => 'required|integer|exists:nationalities,id',
    ]);

    // Check validation
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors occurred.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $validated = $validator->validated();

    $roomId = $validated['room_id'];
    $checkIn = Carbon::parse($validated['check_in']);
    $checkOut = Carbon::parse($validated['check_out']);
    $quantity = $validated['quantity'];
    $customerId = $validated['customer_id'] ?? null;
    $adults = $validated['adults'];
    $children = $validated['children'] ?? 0;
    $nationality = $validated['nationality_id'];

    DB::beginTransaction();

    try {
        $remainingQuantity = $quantity;

        // First pass: Check availability
        $currentDate = clone $checkIn;
        while ($currentDate <= $checkOut) {
            $dailyAvailable = RoomAvailability::where('room_id', $roomId)
                ->whereDate('from', '<=', $currentDate)
                ->whereDate('to', '>=', $currentDate)
                ->sum('quantity');
            $dailyBooked = ModelsBookingEngine::where('room_id', $roomId)
                ->whereDate('check_in', '<=', $currentDate)
                ->whereDate('check_out', '>', $currentDate)
                ->sum('quantity');

            if ($dailyAvailable - $dailyBooked < $remainingQuantity) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Not enough rooms available on {$currentDate->toDateString()}",
                ], 400);
            }

            $currentDate = $currentDate->addDay();
        }

        // Second pass: Deduct quantity
        $currentDate = clone $checkIn;

        while ($currentDate <= $checkOut) {
            $availabilities = RoomAvailability::where('room_id', $roomId)
                ->whereDate('from', '<=', $currentDate)
                ->whereDate('to', '>=', $currentDate)
                ->orderBy('from', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($availabilities as $availability) {
                if ($remainingQuantity <= 0) {
                    break;
                }

                if ($availability->quantity >= $remainingQuantity) {
                    $availability->quantity -= $remainingQuantity;
                    $availability->save();
                    $remainingQuantity = 0;
                } else {
                    $remainingQuantity -= $availability->quantity;
                    $availability->quantity = 0;
                    $availability->save();
                }
            }

            $currentDate = $currentDate->addDay();
        }

        if ($remainingQuantity > 0) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Not enough rooms available after adjusting availability.",
            ], 400);
        }

        // Create the booking record
        $booking = ModelsBookingEngine::create([
            'room_id'   => $roomId,
            'check_in'  => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'quantity'  => $quantity,
        ]);

        // Associate with CustomerBookingEngine
        $customerBooking = CustomerBookingengine::create([
            'customer_id'        => $customerId,
            'booking_engine_id'  => $booking->id,
            'adults'             => $adults,
            'check_in'           => $checkIn->toDateString(),
            'check_out'          => $checkOut->toDateString(),
            'children'           => $children,
            'nationality'        => $nationality,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Booking successful!',
            'booking' => $booking,
            'customer_booking' => $customerBooking,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'An error occurred during the booking process.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
