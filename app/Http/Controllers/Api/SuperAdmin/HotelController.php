<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\HotelPolicy;
use App\Models\Zone;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    use image;
    public function getHotel()
{
    $hotels = Hotel::with([
        'city:id,name,country_id',
        'country:id,name',
        'zone:id,name,city_id,country_id',
        'themes:id,name',
        'facilities:id,name',
        'acceptedCards:id,card_name',
        'features:id,name,description,image',
        'images',
        'policies'
    ])->get();

    $baseUrl = url('/');

    $hotels->each(function ($hotel) use ($baseUrl) {
        if (!empty($hotel->hotel_logo)) {
            $hotel->hotel_logo = url('storage/' . $hotel->hotel_logo);
        }

        $hotel->features->each(function ($feature) use ($baseUrl) {
            if (!empty($feature->image)) {
                $feature->image = $baseUrl . '/' . $feature->image;
            }
        });

        $hotel->images->each(function ($image) use ($baseUrl) {
            if (!empty($image->image)) {
                $image->image = $baseUrl . '/' . $image->image;
            }
        });

        $hotel->themes->each->setHidden(['pivot']);
        $hotel->facilities->each->setHidden(['pivot']);
        $hotel->acceptedCards->each->setHidden(['pivot']);
    });

    return response()->json([
        'hotels' => $hotels
    ]);
}



    public function storeHotel(Request $request)
{
    $validator = Validator::make($request->all(), [
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
        'images' => 'nullable|array',
        'images.*' => 'nullable|string', // Assuming image URLs or paths are strings
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

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }


    $validated = $validator->validated();

    // Ensure policies and other arrays are empty if not provided
    $validated['policies'] = $validated['policies'] ?? [];
    $validated['features'] = $validated['features'] ?? [];
    $validated['facilities'] = $validated['facilities'] ?? [];
    $validated['accepted_cards'] = $validated['accepted_cards'] ?? [];
    $validated['themes'] = $validated['themes'] ?? [];
    $validated['images'] = $validated['images'] ?? [];

    DB::beginTransaction();

    try {
        $hotelLogoPath = null;
        if (!empty($validated['hotel_logo'])) {
            $hotelLogoPath = $this->storeBase64Image($validated['hotel_logo'], 'admin/hotels/logos');
        }

        // Create hotel
        $hotel = Hotel::create([
            'hotel_name' => $validated['hotel_name'],
            'description' => $validated['description'] ?? null,
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'hotel_logo' => $hotelLogoPath,
            'country_id' => $validated['country_id'],
            'city_id' => $validated['city_id'],
            'zone_id' => $validated['zone_id'] ?? null,
            'stars' => $validated['stars'],
            'hotel_video_link' => $validated['hotel_video_link'] ?? null,
            'hotel_website' => $validated['hotel_website'] ?? null,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
        ]);

        // Handle images
        if (!empty($validated['images'])) {
            foreach ($validated['images'] as $base64Image) {
                $imagePath = $this->storeBase64Image($base64Image, 'admin/hotels/images');
                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'image' => $imagePath,
                ]);
            }
        }

        // Handle policies
        if (!empty($validated['policies'])) {
            foreach ($validated['policies'] as $policy) {
                HotelPolicy::create([
                    'hotel_id' => $hotel->id,
                    'title' => $policy['title'] ?? null,
                    'description' => $policy['description'] ?? null,
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
            $themes = array_map(function ($themeId) use ($hotel) {
                return [
                    'hotel_id' => $hotel->id,
                    'theme_id' => $themeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $validated['themes']);
            DB::table('hotel_themes')->insert($themes);
        }

        DB::commit();

        return response()->json([
            'message' => 'Hotel added successfully',
            'hotel' => $hotel,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to add hotel',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function deleteHotel($id){
        $hotel=Hotel::find($id);
        $hotel->delete();
        return response()->json([
            'message' => 'Hotel deleted successfully',
        ]);
    }



}
