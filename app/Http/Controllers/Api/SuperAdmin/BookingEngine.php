<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookinEngine\BookingEngineListRequest;
use App\Models\Agent;
use App\Models\Booking;
use App\Models\BookingEngine as ModelsBookingEngine;
use App\Models\BookingengineList;
use App\Models\BookTourengine;
use App\Models\BookTourExtra;
use App\Models\BookTourRoom;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerBookingengine;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Nationality;
use App\Models\Room;
use App\Models\RoomAvailability;
use App\Models\Tour;
use App\Models\TourAvailability;
use App\Models\TourType;
use App\Models\Wallet;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;

use App\Models\CustomerData; 
use App\Models\SupplierAgent;
use App\Models\Adult;
use App\Models\Child;
use App\Models\ManuelCart;
use App\Models\PaymentsCart;
use App\Models\ManuelDataCart;
use App\Models\BookingPayment; 
use App\Models\FinantiolAcounting; 
use App\trait\image;

class BookingEngine extends Controller
{

    use image;
    public function __construct(
     private SupplierAgent $supplier_agent,
    private Customer $customers,
    private CustomerData $customer_data,
    private PaymentsCart $payments_cart,
    private FinantiolAcounting $financial_accounting,
    private ManuelCart $manuel_cart,
    private BookingPayment $booking_payment,
    private RoomAvailability $room_availability,
    private ModelsBookingEngine $booking_engine,
    private Room $room,
    private Wallet $wallet
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
                        'hotel_description' => $hotel->description,
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
                                    'nationalities_id' => $pricing->nationality
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


    public function getCustomers(){
        $customers = Customer::all();
        return response()->json([
            'customers' => $customers,
        ]);
    }

    public function getAgents(){
        $agents = Agent::all();
        return response()->json([
            'agents' => $agents,
        ]);
    }





    public function bookRoom(Request $request, BookingEngineListRequest $booklist_request)
{
    $user = $request->user(); 
    $validator = Validator::make($request->all(), [
        'room_id'   => 'required|integer|exists:rooms,id',
        'check_in'  => 'required|date|before:check_out',
        'check_out' => 'required|date|after:check_in',
        'quantity'  => 'required|integer|min:1',
        'children' => 'required|array',
        'children.first_name' => 'required',
        'children.last_name' => 'required',
        'children.age' => 'sometimes',
        'adults' => 'required|array',
        'adults.first_name' => 'required',
        'adults.last_name' => 'required',
        'adults.phone' => 'sometimes',
        'adults.title' => 'sometimes',
    ]); 

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $validatedData = $booklist_request->validated();

    try {
        DB::beginTransaction();

        // Create booking
        $booking = $this->booking_engine->create([
            'room_id'   => $request->room_id,
            'check_in'  => $request->check_in,
            'check_out' => $request->check_out,
            'quantity'  => $request->quantity,
        ]);

        // Determine user role type
        $roleMappings = [
            'agent'      => 'agent_id',
            'supplier'   => 'agent_id',
            'affilate'   => 'supplier_id',
            'freelancer' => 'supplier_id',
        ];

        // Assign user role if applicable
        $userRoleColumn = $roleMappings[$user->role] ?? null;
        if ($userRoleColumn) {
            $validatedData[$userRoleColumn] = $user->id;
        }
        // Generate booking code
        $validatedData['code'] = 'E' . rand(10000, 99999) . strtolower(Str::random(1));
        $validatedData['count'] = $request->quantity;
        if(!empty($request->hotel_id)){
            $hotel = Hotel::where('id', $request->hotel_id)
            ->first();
            $validatedData['country_id'] = $hotel?->country_id ?? null;
            $validatedData['city_id'] = $hotel?->city_id ?? null;
        }
        $room = $this->room
        ->where('id', $request->room_id)
        ->first();
        $validatedData['from_supplier_id'] = $room->agent_id;
        $validatedData['to_customer_id'] = $request->user()->agent_id ?? $request->user()->id;

        // Create BookingengineList
        $bookingList = BookingengineList::create($validatedData);
        $bookingList->adult()->createMany($request->adults->toArray());
        $bookingList->children()->createMany($request->children->toArray());

        DB::commit();

        return response()->json([
            'message' => 'Room booked successfully',
            'booking' => $booking,
            'booking_list_id' => $bookingList->id
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'An error occurred while booking the room', 'details' => $e->getMessage()], 500);
    }


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

    public function engine_payment(Request $request){
        $validation = Validator::make($request->all(), [
            'booking_engine_id' => ['required'],
            'type' => ['required', 'in:room,tour'],
            'payment_type' => ['required', 'in:full,partial,later'],
            'total_cart' => ['required','numeric'],
            'payment_methods' => ['required'],
            'payment_methods.*.amount' => ['required', 'numeric'],
            'payment_methods.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payment_methods.*.image' => ['sometimes'],
            'payments.*.amount' => ['required', 'numeric'],
            'payments.*.date' => ['required', 'date'],
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
            $agent_data = $this->affilate_agent
            ->where('id', $request->user()->affilate_id)
            ->first();
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
            $agent_data = $this->agent
            ->where('id', $request->user()->agent_id)
            ->first();
        }
        else{
            $agent_id = $request->user()->id;
            $agent_data = $request->user();
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $role = 'affilate_id';
        }
        else {
            $role = 'agent_id';
        }

        $booking_engine = null;
        $total = 0;
        if($request->type == 'room'){
            $booking_engine = BookingengineList::
            where('id', $request->booking_engine_id)
            ->first();
            $total = $booking_engine->amount;
        }
        elseif($request->type == 'tour'){
            $booking_engine = BookTourengine::
            where('id', $request->booking_engine_id)
            ->first();
            $total = $booking_engine->total_price;
        }
        // ..........................................................
        // ..........................................................
        // ..........................................................
        if($role = 'agent_id' && $booking_engine->to_agent_id != $agent_id) {
           $my_wallet = $this->wallet
           ->where('currancy_id', $booking_engine->currancy_id)
           ->where($role, $agent_id)
           ->first();       
           if($my_wallet->amount < $total){
                return response()->json([
                    'errors' => 'You must charge your wallet'
                ], 400);
           }
           $his_wallet = $this->wallet
           ->where('currancy_id', $booking_engine->currancy_id)
           ->where('agent_id', $booking_engine->from_supplier_id )
           ->first();
           $my_wallet->amount -= $total;
           $his_wallet->amount += $total;
        }
        $booking_engine->cart_status = 1;
        $booking_engine->save();
         $amount_payment = 0;
            if ($request->payment_methods) {
                foreach ($payment_methods as $item) {
                    $amount_payment += $item['amount'];
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                    while (!empty($booking_payment_item)) {
                        $code = Str::random(8);
                        $booking_payment_item = $this->booking_payment
                        ->where('code', $code)
                        ->first();
                    }
                    $booking_payment = $this->booking_engine
                    ->booking_payment()
                    ->create([
                        $role => $agent_id,
                        'date' => date('Y-m-d'),
                        'amount' => $item['amount'],
                        'financial_id' => $item['payment_method_id'],
                        'code' => $code,
                        'to_customer_id' => $booking_engine->to_customer_id ,
                        'first_time' => 1,
                    ]);
// ___________________________________________________________________________________ \
                    $cartRequest = [
                        'total' => $request->total_cart,
                        'payment' => $item['amount'],
                        'payment_method_id' => $item['payment_method_id'],
                    ];
                    $manuel_cart = $booking_engine
                    ->payment_carts()
                    ->create($cartRequest);
                }
            }
            else {
                $code = Str::random(8);
                $booking_payment_item = $this->booking_payment
                ->where('code', $code)
                ->first();
                while (!empty($booking_payment_item)) {
                    $code = Str::random(8);
                    $booking_payment_item = $this->booking_payment
                    ->where('code', $code)
                    ->first();
                }
                $booking_payment = $this->booking_engine
                ->booking_payment()
                ->create([ 
                    $role => $agent_id,
                    'date' => date('Y-m-d'),
                    'amount' => 0,
                    'code' => $code, 
                    'to_customer_id' => $booking_engine->to_customer_id ,
                    'first_time' => 1,
                ]);
            }
            if ($request->payment_type == 'partial' || $request->payment_type == 'later') {
                $validation = Validator::make($request->all(), [
                    'payments' => 'required',
                ]);
                if($validation->fails()){
                    return response()->json(['errors'=>$validation->errors()], 401);
                }
                $payments = is_string($request->payments) ? json_decode($request->payments)
                : $request->payments;
                foreach ($payments as $item) {
                    $booking_engine->upcoming_payment_carts()
                    ->create([ 
                        $role => $agent_id,
                        'to_customer_id' => $booking_engine->to_customer_id, 
                        'amount' => $item['amount'],
                        'date' => $item['date'],
                    ]);
                }
            }
            $customer = $this->customer_data
            ->whereIn('status', ['active', 'inactive'])
            ->where('customer_id', $booking_engine->to_customer_id)
            ->where($role, $agent_id)
            ->first();
            if (!empty($customer)) {
                $customer->update([
                    'type' => 'customer',
                    'total_booking' => $amount_payment + $customer->total_booking,
                ]);
                $this->customers
                ->where('id', $booking_engine->to_customer_id)
                ->update([
                    'role' => 'customer'
                ]);
                $position = 'Customer';
            } 
            $data = [];
            $data['name'] = $customer->name;
            $data['position'] = $position;
            $data['amount'] = $amount_payment;
            $data['payment_date'] = date('Y-m-d');
            $data['agent'] = $agent_data->name;
            Mail::to($agent_data->email)->send(new PaymentMail($data));
            $agent_data = [];
            if (!empty($manuel_booking->affilate_id)) {
                $agent = $manuel_booking->affilate; 
            }
            else{
                $agent = $manuel_booking->agent; 
            }
            $agent_data = [
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone,
            ];
           
           if (!empty($manuel_booking->to_supplier_id)) {
               $client_data = $manuel_booking->to_client;
               $client['name'] = $client_data->name;
               $client['phone'] = $client_data->phones[0] ?? $client_data->phones;
               $client['email'] = $client_data->emails[0] ?? $client_data->emails;
           }
           else{
               $client_data = $manuel_booking->to_client;
               $client['name'] = $client_data->name;
               $client['phone'] = $client_data->phone;
               $client['email'] = $client_data->email;
           }
            return response()->json([ 
                'cart_id' => $booking_engine->id,
                'total' => $booking_engine->amount, 
            ]);
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
            'status' => 'nullable|in:pending,confirmed,canceled,vouchered',
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
                'destinations.country:id,name',
                'destinations.city:id,name',
                'itinerary',
                'includes',
                'excludes',
                'cancelation_items',
                'tour_images',
                'tour_hotels',
                'tour_discounts',
                'tour_pricings.tour_pricing_items.currency',
                'tour_extras.currency:id,name',
                // 'currency:id,name' // Add this line
            ])
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




    public function bookTour(Request $request)
{
    $validation = Validator::make($request->all(), [
        'tour_id' => 'required|exists:tours,id',
        'no_of_people' => 'required|integer|min:1',
        'special_request' => 'nullable|string',
        'currency_id' => 'nullable|exists:currency_agents,id',
        'total_price' => 'required|integer|min:1',
        'customer_id' => 'required|exists:customers,id',
        'agents_id' => 'nullable|exists:agents,id',
        'to_hotel_id' => 'nullable|exists:tour_hotels,id',
        'single_room_count' => 'nullable|integer',
        'double_room_count' => 'nullable|integer',
        'triple_room_count' => 'nullable|integer',
        'quad_room_count' => 'nullable|integer',
        'extras' => 'nullable|array',
        'extras.*.extra_id' => 'required|exists:tour_extras,id',
        'extras.*.count' => 'required|integer|min:1',
        
        'children.first_name' => 'required',
        'children.last_name' => 'required',
        'children.age' => 'sometimes',
        'adults' => 'required|array',
        'adults.first_name' => 'required',
        'adults.last_name' => 'required',
        'adults.phone' => 'sometimes',
        'adults.title' => 'sometimes',
    ]);

    if ($validation->fails()) {
        return response()->json(['errors' => $validation->errors()], 400);
    }
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id; 
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $role = 'affilate_id';
        }
        else {
            $role = 'agent_id';
        } 

    $tour = Tour::findOrFail($request->tour_id);
    $customer = Customer::find($request->customer_id);
    $agent = Agent::find($request->agents_id);

    if (!$customer && !$agent) {
        return response()->json(['error' => 'Either customer or agent must be provided.'], 400);
    }

    // Determine Recipient
    $recipient = $customer ?? $agent;
    $toRole = $customer ? 'Customer' : 'Agent';
 
    $createBooking = BookTourengine::create([
        'tour_id' => $tour->id,
        'no_of_people' => $request->no_of_people,
        'special_request' => $request->special_request ?? null,
        'currency_id' => $request->currency_id,
        'total_price' => $request->total_price,
        'to_name' => $recipient->name,
        'to_email' => $recipient->email,
        'to_phone' => $recipient->phone,
        'to_role' => $toRole,
        'from_supplier_id' => $tour->agent_id,
        'to_customer_id' => $request->customer_id,
        $role => $agent_id,
        'code' => 'TE' . rand(10000, 99999) . strtolower(Str::random(1)),
        'status' => 'pending',
        'payment_status' => 'full',
        'to_hotel_id' => optional($tour->tour_hotels()->first())->id??null,
        'country_id' => null,
    ]);
    $createBooking->adult()->createMany($request->adults->toArray());
    $createBooking->children()->createMany($request->children->toArray());
    $updateremaining = TourAvailability::where('tour_id', $tour->id);
    $updateremaining->decrement('remaining', $request->no_of_people);

    if ($request->has('extras')) {
        foreach ($request->extras as $extra) {
            BookTourExtra::create([
                'book_tour_id' => $createBooking->id,
                'extra_id' => $extra['extra_id'],
                'count' => $extra['count'],
            ]);
        }
    }

    if ($request->hasAny(['single_room_count', 'double_room_count', 'triple_room_count', 'quad_room_count'])) {
        BookTourRoom::create([
            'book_tour_id' => $createBooking->id,
            'to_hotel_id' => $request->to_hotel_id,
            'single_room_count' => $request->single_room_count ?? 0,
            'double_room_count' => $request->double_room_count ?? 0,
            'triple_room_count' => $request->triple_room_count ?? 0,
            'quad_room_count' => $request->quad_room_count ?? 0,
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Tour booked successfully',
        'tour' => BookTourengine::with([
            'tour',
            'tour.agent:id,name,email,phone',
            'currency', // Get full currency details
            'country', // Get full country details
            'book_tour_extra.extra', // Get full details of booked extras
            'book_tour_room.to_hotel'
        ])->find($createBooking->id),
    ], 200);
}

    public function getNationalities(){
        $nationality = Nationality::all();
        $data = [
            'nationality' => $nationality
        ];
        return response()->json($data);
    }

}
