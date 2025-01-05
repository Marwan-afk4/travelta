<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

use App\Models\ManuelBooking;

class BookingController extends Controller
{
    public function __construct(private ManuelBooking $manuel_booking){}

    public function getBookings(){
        // $Boooking=Booking::with('user')->get();
        // $data = $Boooking->map(function ($booking) {
        //     return [
        //         'booking_id' => $booking->id,
        //         'user_id' => $booking->user_id,
        //         'user_name' => $booking->user->name,
        //         'user_email' => $booking->user->email,
        //         'user_phone' => $booking->user->phone,
        //         'user_emergency_phone' => $booking->user->emergency_phone,
        //         'booking_date' => $booking->date,
        //         'booking_type' => $booking->type,
        //         'booking_destanation' => $booking->destanation,
        //     ];
        // });
        // return response()->json(['bookings' => $data]);
        
        $hotel = $this->manuel_booking
        ->with(['hotel', 'taxes', 'from_supplier', 'to_client'])
        ->get();
        $bus = $this->manuel_booking
        ->with(['bus', 'taxes', 'from_supplier', 'to_client'])
        ->get();
        $visa = $this->manuel_booking
        ->with(['visa', 'taxes', 'from_supplier', 'to_client'])
        ->get();
        $flight = $this->manuel_booking
        ->with(['flight', 'taxes', 'from_supplier', 'to_client'])
        ->get();
       $tour = $this->manuel_booking
        ->with(['tour' => function($query){
            $query->with([
                'hotel', 'bus'
            ]);
        }, 'taxes', 'from_supplier', 'to_client'])
        ->whereHas('tour')
        ->get();
        foreach ($hotel as $item) {
            $item->start_date = $item->hotel->check_in;
            $item->end_date = $item->hotel->check_out;
        }
        foreach ($bus as $item) {
            $item->start_date = $item->bus->departure;
            $item->end_date = $item->bus->arrival;
        }
        foreach ($visa as $item) {
            $item->start_date = $item->visa->travel_date;
            $item->end_date = $item->visa->travel_date;
        }
        foreach ($flight as $item) {
            $item->start_date = $item->flight->departure;
            $item->end_date = $item->flight->arrival;
        }
        foreach ($tour as $item) {
            $item->start_date = $item->tour->hotel->sortBy('check_in')->first()->check_in;
            $item->end_date = $item->tour->hotel->sortByDesc('check_out')->first()->check_out;
        }

        return response()->json([
            'hotel' => $hotel,
            'bus' => $bus,
            'visa' => $visa,
            'flight' => $flight,
            'tour' => $tour,
        ]);
    }
}
