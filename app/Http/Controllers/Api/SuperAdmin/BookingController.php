<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    public function getBookings(){
        $Boooking=Booking::with('user')->get();
        $data = $Boooking->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'user_name' => $booking->user->name,
                'user_email' => $booking->user->email,
                'user_phone' => $booking->user->phone,
                'user_emergency_phone' => $booking->user->emergency_phone,
                'booking_date' => $booking->date,
                'booking_type' => $booking->type,
                'booking_destanation' => $booking->destanation,
            ];
        });
        return response()->json(['bookings' => $data]);
    }
}
