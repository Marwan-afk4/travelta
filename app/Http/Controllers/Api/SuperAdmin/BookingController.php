<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Resources\ManuelBusResource;
use App\Http\Resources\ManuelFlightResource;
use App\Http\Resources\ManuelHotelResource;
use App\Http\Resources\ManuelTourResource;
use App\Http\Resources\ManuelVisaResource;

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
        ->with(['hotel', 'taxes', 'from_supplier', 'country'])
        ->whereHas('hotel')
        ->get();
        $bus = $this->manuel_booking
        ->with(['bus', 'taxes', 'from_supplier', 'country'])
        ->whereHas('bus')
        ->get();
        $visa = $this->manuel_booking
        ->with(['visa', 'taxes', 'from_supplier', 'country'])
        ->whereHas('visa')
        ->get();
        $flight = $this->manuel_booking
        ->with(['flight', 'taxes', 'from_supplier', 'country'])
        ->whereHas('flight')
        ->get();
       $tour = $this->manuel_booking
        ->with(['tour' => function($query){
            $query->with([
                'hotel', 'bus'
            ]);
        }, 'taxes', 'from_supplier', 'country'])
        ->whereHas('tour')
        ->get(); 
        $hotel = ManuelHotelResource::collection($hotel);
        $bus = ManuelBusResource::collection($bus);
        $visa = ManuelVisaResource::collection($visa);
        $flight = ManuelFlightResource::collection($flight);
        $tour = ManuelTourResource::collection($tour);

        return response()->json([
            'hotel' => $hotel,
            'bus' => $bus,
            'visa' => $visa,
            'flight' => $flight,
            'tour' => $tour,
        ]);
    }
}
