<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\CurrencyAgent;
use App\Models\TourType;
use App\Models\Country;
use App\Models\City;
use App\Models\Tour;
use App\Models\TourPricingItems;

class TourController extends Controller
{
    public function __construct(private TourType $tour_types, 
    private Country $countries, private City $cities, private Tour $tour,
    private CurrencyAgent $currencies, private TourPricingItems $pricing_item){}

    public function view(Request $request){
        // /agent/tour
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
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $tour_types = $this->tour_types
        ->get();
        $countries = $this->countries
        ->get();
        $cities = $this->cities
        ->get();
        $tour = $this->tour
        ->where($agent_type, $agent_id)
        ->with(['destinations' => function($query){
            $query->with('city', 'country');
        }, 'availability', 'cancelation_items',
        'excludes', 'includes', 'itinerary', 'tour_types', 'pick_up_country',
        'pick_up_city', 'tour_room'])
        ->get()
        ->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item->name,
                'status' => $item->status,
                'accepted' => $item->accepted,
                'tour_room' => $item->tour_room,
                'arrival' => $item->arrival,
                'excludes' => $item->excludes,
                'includes' => $item->includes,
                'pick_up_country' => $item->pick_up_country,
                'pick_up_city' => $item->pick_up_city,
                'to_cities' => $item?->destinations?->city?->country,
            ];
        });

        return response()->json([
            'tour_types' => $tour_types,
            'countries' => $countries,
            'cities' => $cities,
            'tour' => $tour,
        ]);
    }

    public function tour(Request $request, $id){ 
        // /agent/tour/item/{id}
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
            $agent_type = 'affilate_id';
        }
        else{
            $agent_type = 'agent_id';
        }
        $tour = $this->tour
        ->with(['destinations' => function($query){
            $query->with('city', 'country');
        }, 'availability', 'cancelation_items',
        'excludes', 'includes', 'itinerary', 'tour_types', 'pick_up_country',
        'pick_up_city', 'tour_images', 'tour_hotels', 'tour_extras', 'tour_discounts',
        'tour_pricings.tour_pricing_items', 'tour_room'])
        ->where('id', $id)
        ->first();
        $pricing_item = $this->pricing_item
        ->where('tour_id', $id)
        ->where('type', '0')
        ->first();
        $tour->price = $pricing_item->price ?? null;
        $tour->currency_id = $pricing_item->currency_id ?? null;

        return response()->json([
            'tour' => $tour,
        ]);
    }

    public function lists(Request $request){
        // /agent/tour/lists
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
        $tour_types = $this->tour_types
        ->get();
        $countries = $this->countries
        ->get();
        $cities = $this->cities
        ->get();
        $currencies = $this->currencies
        ->select('id', 'name')
        ->where($role, $agent_id)
        ->get();
    
        return response()->json([
            'tour_types' => $tour_types,
            'countries' => $countries,
            'cities' => $cities,
            'currencies' => $currencies,
        ]);
    }

    public function status(Request $request, $id){
        // /agent/tour/status/{id}
        $validation = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
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
        $tour = $this->tour
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => $request->status ? 'active' : 'banned',
        ]);
    }

    public function accepted(Request $request, $id){
        // /agent/tour/accepted/{id}
        $validation = Validator::make($request->all(), [
            'accepted' => 'required|boolean',
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
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
        $tour = $this->tour
        ->where('id', $id)
        ->where($role, $agent_id)
        ->update([
            'accepted' => $request->accepted
        ]);

        return response()->json([
            'success' => $request->accepted ? 'active' : 'banned',
        ]);
    }
}
