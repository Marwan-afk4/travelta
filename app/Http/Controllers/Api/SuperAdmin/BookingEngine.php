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
use App\Models\Tour;
use App\Models\TourType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingEngine extends Controller
{

    public function __construct(
        private RoomAvailability $room_availability,
        private ModelsBookingEngine $booking_engine
    ) {}


    public function gethotels()
    {
        $hotels = Hotel::all();
        $data = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'name' => $hotel->hotel_name,
            ];
        });
        return response()->json(['hotels' => $data]);
    }

    public function getcities()
    {
        $cities = City::all();
        $data = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
            ];
        });
        return response()->json(['cities' => $data]);
    }

    public function getcountries()
    {
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
            'country_id' => 'nullable|integer|exists:countries,id',
            'max_adults' => 'required|integer|min:1',
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
                'images',
                'rooms.gallery',
                'rooms.amenity',
                'facilities',
                'features',
                'policies',
                'acceptedCards',
                'themes',
                'rooms.pricing'
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
                        'room_pricings' => $roomPricings = $hotel->rooms->flatMap(function ($room) {
                            return $room->pricing->map(function ($pricing) {
                                return [
                                    'id' => $pricing->id,
                                    'room_id' => $pricing->room_id,
                                    'name' => $pricing->name,
                                    'from' => $pricing->from,
                                    'to' => $pricing->to,
                                    'price' => $pricing->price,
                                    'currency_id' => $pricing->currency_id,
                                    'currency_name' => $pricing->currency->name,
                                ];
                            });
                        })
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








    public function bookRoom(Request $request, BookingEngineListRequest $booklist_request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'room_id'       => 'required|integer|exists:rooms,id',
            'check_in'      => 'required|date|before:check_out',
            'check_out'     => 'required|date|after:check_in',
            'quantity'      => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }
        $check_in = $request->check_in;
        $check_out = $request->check_out;
        $room_id = $request->room_id;

        $startdate = Carbon::parse($check_in);
        $enddate = Carbon::parse($check_out);

        $booking = $this->booking_engine->create([
            'room_id' => $room_id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'quantity' => $request->quantity,

        ]);

        $ListValidation = $booklist_request->validated();
        $userRole = 0;
        if ($user->role == 'agent' || $user->role == 'supplier') {
            $bookingList = BookingengineList::create([
                'agent_id' => $user->id,
                'from_supplier_id' => $ListValidation['from_supplier_id'] ?? null,
                'country_id' => $ListValidation['country_id'],
                'city_id' => $ListValidation['city_id'],
                'hotel_id' => $ListValidation['hotel_id'],
                'to_agent_id' => $ListValidation['to_agent_id'] ?? null,
                'to_customer_id' => $ListValidation['to_customer_id'] ?? null,
                'check_in' => $ListValidation['check_in'],
                'check_out' => $ListValidation['check_out'],
                'room_type' => $ListValidation['room_type'],
                'no_of_adults' => $ListValidation['no_of_adults'],
                'no_of_children' => $ListValidation['no_of_children'],
                'no_of_nights' => $ListValidation['no_of_nights'],
                'payment_status' => $ListValidation['payment_status'] ?? 'full',
                'status' => $ListValidation['status'] ?? 'done',
                'special_request' => $ListValidation['special_request'] ?? null,
                'currancy_id' => $ListValidation['currancy_id'],
                'amount' => $ListValidation['amount'],
            ]);
        } elseif ($user->role == 'affilate' || $user->role == 'freelancer') {
            $bookingList = BookingengineList::create([
                'supplier_id' => $user->id,
                'from_supplier_id' => $ListValidation['from_supplier_id'] ?? null,
                'country_id' => $ListValidation['country_id'],
                'city_id' => $ListValidation['city_id'],
                'hotel_id' => $ListValidation['hotel_id'],
                'to_agent_id' => $ListValidation['to_agent_id'] ?? null,
                'to_customer_id' => $ListValidation['to_customer_id'] ?? null,
                'check_in' => $ListValidation['check_in'],
                'check_out' => $ListValidation['check_out'],
                'room_type' => $ListValidation['room_type'],
                'no_of_adults' => $ListValidation['no_of_adults'],
                'no_of_children' => $ListValidation['no_of_children'],
                'no_of_nights' => $ListValidation['no_of_nights'],
                'payment_status' => $ListValidation['payment_status'] ?? 'full',
                'status' => $ListValidation['status'] ?? 'done',
                'special_request' => $ListValidation['special_request'] ?? null,
                'currancy_id' => $ListValidation['currancy_id'],
                'amount' => $ListValidation['amount'],
            ]);
        }



        return response()->json([
            'message' => 'the room has been booked successfully',
            'booking_list' => 'booking_list has been created successfully',
            'booking' => $booking,
        ]);

        // $roomAvailability = $this->room_availability
        //         ->where('room_id', $room_id)
        //         ->where(function ($query) use ($startdate, $enddate) {
        //             $query->whereBetween('from', [$startdate, $enddate])
        //                 ->orWhereBetween('to', [$startdate, $enddate])
        //                 ->orWhere(function ($subQuery) use ($startdate, $enddate) {
        //                     $subQuery->where('from', '<=', $startdate)
        //                             ->where('to', '>=', $enddate);
        //                 });
        //         })
        //         ->get();

        //         $bookings = $this->booking_engine
        //         ->where('room_id', $room_id)
        //         ->where(function ($query) use ($startdate, $enddate) {
        //             $query->whereBetween('check_in', [$startdate, $enddate])
        //                 ->orWhereBetween('check_out', [$startdate, $enddate])
        //                 ->orWhere(function ($subQuery) use ($startdate, $enddate) {
        //                     $subQuery->where('check_in', '<=', $startdate)
        //                             ->where('check_out', '>=', $enddate);
        //                 });
        //         })
        //         ->get();

        //         $remainingQuantity = $roomAvailability->sum('quantity') - $bookings->sum('quantity');
        //         if ($remainingQuantity < $request->quantity) {
        //             return response()->json(['message' => 'No rooms available for the specified dates.'], 400);
        //         }
    }

    public function getAvailableTours(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'people' => 'required|integer|min:1',
            'destination_country' => 'nullable|integer|exists:countries,id',
            'destination_city' => 'nullable|integer|exists:cities,id',
            'tour_type_id' => 'nullable|integer|exists:tour_types,id',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $year = $request->year;
        $month = $request->month;
        $people = $request->people;
        $destinationCountry = $request->destination_country;
        $destinationCity = $request->destination_city;
        $tourTypeId = $request->tour_type_id;
        $today = Carbon::now()->toDateString();

        $tours = Tour::where('status', 1)
            ->where('accepted', 1)
            ->whereHas('availability', function ($query) use ($year, $month, $people, $today) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->where('remaining', '>=', $people)
                    ->whereDate('last_booking', '>=', $today);
            })
            ->when($destinationCountry, function ($query) use ($destinationCountry) {
                $query->whereHas('destinations', function ($q) use ($destinationCountry) {
                    $q->where('country_id', $destinationCountry);
                });
            })
            ->when($destinationCity, function ($query) use ($destinationCity) {
                $query->whereHas('destinations', function ($q) use ($destinationCity) {
                    $q->where('city_id', $destinationCity);
                });
            })
            ->when($tourTypeId, function ($query) use ($tourTypeId) {
                $query->where('tour_type_id', $tourTypeId);
            })
            ->with([
                'availability' => function ($q) use ($year, $month) {
                    $q->whereYear('date', $year)->whereMonth('date', $month);
                },
                'destinations',
                'tour_type'
            ])
            ->with('itinerary')
            ->with('includes')
            ->with('excludes')
            ->with('cancelation_items')
            ->with('tour_images')
            ->with('tour_hotels')
            ->with('tour_discounts')
            ->with('tour_pricings')
            ->with('tour_extras')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $tours->count(),
            'tours' => $tours
        ], 200);
    }

    public function getTourtype(){
        $tourtype = TourType::all();
        $data = [
            'tourtype' => $tourtype
        ];
        return response()->json($data);
    }
}
