<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookinEngine\BookingEngineListRequest;
use App\Models\Booking;
use App\Models\BookingEngine as ModelsBookingEngine;
use App\Models\BookingengineList;
use App\Models\City;
use App\Models\Country;
use App\Models\CustomerBookingengine;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Room;
use App\Models\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingEngine extends Controller
{
    public function gethotels(){
        $hotels = Hotel::all();
        $data = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'name' => $hotel->hotel_name,
            ];
        });
        return response()->json(['hotels' => $data]);
    }

    public function getcities(){
        $cities = City::all();
        $data = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
            ];
        });
        return response()->json(['cities' => $data]);
    }

    public function getcountries(){
        $countries = Country::all();
        $data = $countries->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
            ];
        });
        return response()->json(['countries' => $data]);
    }

    public function getAvailableRooms(Request $request)
{
    $validated = $request->validate([
        'check_in'  => 'required|date|before:check_out',
        'check_out' => 'required|date|after:check_in',
        'hotel_id'  => 'nullable|integer|exists:hotels,id',
        'city_id'   => 'nullable|integer|exists:cities,id',
        'country_id'=> 'nullable|integer|exists:countries,id',
        'max_adults'=> 'required|integer|min:1',
        'max_children' => 'required|integer|min:0'
    ]);

    $checkIn = Carbon::parse($validated['check_in']);
    $checkOut = Carbon::parse($validated['check_out']);

    try {
        // ✅ Get pricing data for the requested guests
        $pricingData = DB::table('room_pricing_data')
            ->where('adults', '>=', $validated['max_adults'])
            ->where('children', '>=', $validated['max_children'])
            ->get(['id', 'room_type']);

        if ($pricingData->isEmpty()) {
            return response()->json(['success' => true, 'hotels' => []]);
        }

        // ✅ Map pricing data
        $pricingDataMap = $pricingData->pluck('room_type', 'id')->toArray();

        // ✅ Get room IDs and room types
        $roomPricings = DB::table('room_pricings')
            ->whereIn('pricing_data_id', array_keys($pricingDataMap))
            ->get(['room_id', 'pricing_data_id']);

        if ($roomPricings->isEmpty()) {
            return response()->json(['success' => true, 'hotels' => []]);
        }

        // ✅ Map room_id to room_type
        $roomTypeMap = $roomPricings->mapWithKeys(function ($pricing) use ($pricingDataMap) {
            return [$pricing->room_id => $pricingDataMap[$pricing->pricing_data_id]];
        })->toArray();

        // ✅ Fetch hotels with available rooms & images
        $hotelsQuery = Hotel::with([
            'images', 'rooms.gallery', 'rooms.amenity', 'facilities', 'features', 'policies', 'acceptedCards', 'themes'
        ]);

        if (!empty($validated['hotel_id'])) {
            $hotelsQuery->where('id', $validated['hotel_id']);
        }
        if (!empty($validated['city_id'])) {
            $hotelsQuery->where('city_id', $validated['city_id']);
        }
        if (!empty($validated['country_id'])) {
            $hotelsQuery->whereHas('city', function ($query) use ($validated) {
                $query->where('country_id', $validated['country_id']);
            });
        }

        $hotels = $hotelsQuery->get();
        $results = [];

        foreach ($hotels as $hotel) {
            $availableRooms = [];

            foreach ($hotel->rooms as $room) {
                if (!isset($roomTypeMap[$room->id])) {
                    continue;
                }

                $roomId = $room->id;
                $roomType = $roomTypeMap[$roomId];
                $remainingQuantity = null;
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

                    $dailyRemaining = $dailyAvailable - $dailyBooked;

                    if ($dailyRemaining <= 0) {
                        $remainingQuantity = 0;
                        break;
                    }

                    $remainingQuantity = is_null($remainingQuantity)
                        ? $dailyRemaining
                        : min($remainingQuantity, $dailyRemaining);

                    $currentDate = $currentDate->addDay();
                }

                if ($remainingQuantity > 0) {
                    $availableRooms[] = [
                        'room_id' => $roomId,
                        'room_type' => $roomType,
                        'available_quantity' => $remainingQuantity,
                        'room_details' => $room,
                    ];
                }
            }

            if (!empty($availableRooms)) {
                $results[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel->hotel_name,
                    'hotel_stars' => $hotel->stars,
                    'hotel_logo' => $hotel->hotel_logo ? asset('storage/' . $hotel->hotel_logo) : null,
                    'hotel_facilities' => $hotel->facilities->unique('id')->map(function ($facility) {
                        return [
                            'id' => $facility->id,
                            'name' => $facility->name,
                            'logo' => $facility->logo ? asset('storage/' . $facility->logo) : null,
                        ];
                    })->values(),
                    'hotel_features' => $hotel->features->map(function ($feature) {
                        return [
                            'id' => $feature->id,
                            'name' => $feature->name,
                            'description' => $feature->description,
                            'image' => $feature->image ? asset('storage/' . $feature->image) : null,
                        ];
                    }),
                    'hotel_policies' => $hotel->policies->map(function ($policy) {
                        return [
                            'id' => $policy->id,
                            'title' => $policy->title,
                            'description' => $policy->description,
                            'logo' => asset('storage/' . $policy->logo),
                        ];
                    }),
                    'hotel_accepted_cards' => $hotel->acceptedCards->map(function ($card) {
                        return [
                            'id' => $card->id,
                            'card_name' => $card->card_name,
                            'logo' => asset('storage/' . $card->logo),
                        ];
                    }),
                    'hotel_themes' => $hotel->themes,
                    'city' => $hotel->city->name,
                    'country' => $hotel->city->country->name,
                    'images' => HotelImage::where('hotel_id', $hotel->id)
                        ->pluck('image')
                        ->map(fn($image) => asset('storage/' . $image))
                        ->toArray(),
                    'available_rooms' => $availableRooms,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'hotels' => $results,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}








public function bookRoom(Request $request, BookingEngineListRequest $bookinglistrequest)
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
        // Check if there is enough availability for the requested period
        $availableDates = RoomAvailability::where('room_id', $roomId)
            ->whereDate('from', '<=', $checkOut)
            ->whereDate('to', '>=', $checkIn)
            ->orderBy('from', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($availableDates as $availability) {
            $dailyAvailable = $availability->quantity;
            $dailyBooked = ModelsBookingEngine::where('room_id', $roomId)
                ->whereDate('check_in', '<=', $availability->to)
                ->whereDate('check_out', '>', $availability->from)
                ->sum('quantity');

            $remainingRooms = $dailyAvailable - $dailyBooked;

            if ($remainingRooms < $quantity) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Not enough rooms available for some dates in the requested range.",
                ], 400);
            }
        }

        // Deduct availability for each overlapping period
        foreach ($availableDates as $availability) {
            if ($availability->quantity >= $quantity) {
                $availability->quantity -= $quantity;
                $availability->save();
            }
        }

        // Create booking record
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

        $validationList = $bookinglistrequest->validated();

        // Create booking list entry
        $bookingList = BookingengineList::create([
            'from_supplier_id' => $validationList['from_supplier_id'],
            'country_id' => $validationList['country_id'],
            'city_id' => $validationList['city_id'],
            'hotel_id' => $validationList['hotel_id'],
            'to_agent_id' => $validationList['to_agent_id'] ?? null,
            'to_customer_id' => $validationList['to_customer_id'] ?? null,
            'check_in' => $validationList['check_in'],
            'check_out' => $validationList['check_out'],
            'room_type' => $validationList['room_type'],
            'no_of_adults' => $validationList['no_of_adults'],
            'no_of_children' => $validationList['no_of_children'],
            'no_of_nights' => $validationList['no_of_nights'],
            'payment_status' => $validationList['payment_status'],
            'status' => $validationList['status'] ?? 'inprogress',
            'code' => 'e' . rand(10000, 9999999) . strtolower(Str::random(1)),
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
