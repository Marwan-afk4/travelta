<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\HotelIamge;
use App\Models\HotelPolicy;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function getHotel(){
        //hangib el hotel with kol el tabels el tanya
        $hotels = Hotel::with(['city','country','zone','themes','facilities','acceptedCards','features','images','policies'])->get();
        $data = [
            'hotels' => $hotels
        ];
        return response()->json($data);
    }

    public function storeHotel(Request $request)
{
    $validated = $request->validate([
        'hotel_name' => 'required|string|unique:hotels,hotel_name',
        'description' => 'nullable|string',
        'email' => 'required|email|unique:hotels,email',
        'phone_number' => 'required|string|unique:hotels,phone_number',
        'hotel_logo' => 'nullable',
        'country_id' => 'required|integer|exists:countries,id',
        'city_id' => 'required|integer|exists:cities,id',
        'zone_id' => 'nullable|integer|exists:zones,id',
        'stars' => 'required|integer|min:1|max:5',
        'hotel_video_link' => 'nullable|url',
        'hotel_website' => 'nullable|url',
        'check_in' => 'required',
        'check_out' => 'required',
        'iamges' => 'nullable|array',
        'iamges.*' => 'nullable',
        'policies' => 'nullable|array',
        'policies.*.title' => 'nullable|string',
        'policies.*.description' => 'nullable|string',
        'features' => 'nullable|array',
        'features.*' => 'required|integer|exists:features,id',
        'facilities' => 'nullable|array',
        'facilities.*' => 'nullable|integer|exists:facilities,id',
        'accepted_cards' => 'nullable|array',
        'accepted_cards.*' => 'required|integer|exists:accepted_cards,id',
        'themes' => 'nullable|array',
        'themes.*' => 'nullable|integer|exists:themes,id',
    ]);

    // Ensure policies and other arrays are empty if not provided
    $validated['policies'] = $validated['policies'] ?? [];
    $validated['features'] = $validated['features'] ?? [];
    $validated['facilities'] = $validated['facilities'] ?? [];
    $validated['accepted_cards'] = $validated['accepted_cards'] ?? [];
    $validated['themes'] = $validated['themes'] ?? [];

    DB::beginTransaction();

    try {
        $hotel = Hotel::create($validated);

        // Handle images
        if ($request->has('iamge')) {
            foreach ($validated['iamge'] as $image) {
                HotelIamge::create([
                    'hotel_id' => $hotel->id,
                    'image' => $image, // If passing URL/path instead of files
                ]);
            }
        }

        // Handle policies
        if (!empty($validated['policies'])) {
            foreach ($validated['policies'] as $policy) {
                HotelPolicy::create([
                    'hotel_id' => $hotel->id,
                    'title' => $policy['title'],
                    'description' => $policy['description'],
                ]);
            }
        }

        // Handle features
        if (!empty($validated['features'])) {
            $hotel->features()->attach($validated['features']);
        }

        // Handle facilities
        if (!empty($validated['facilities'])) {
            $hotel->facilities()->sync($validated['facilities']);
        }

        // Handle accepted cards
        if (!empty($validated['accepted_cards'])) {
            $hotel->acceptedCards()->sync($validated['accepted_cards']);
        }

        // Handle themes
        if (!empty($validated['themes'])) {
            foreach ($validated['themes'] as $theme) {
                DB::table('hotel_themes')->insert([
                    'hotel_id' => $hotel->id,
                    'theme_id' => $theme,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Hotel added successfully',
            'hotel' => $hotel,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Failed to add hotel', 'error' => $e->getMessage()], 500);
    }
}



}
