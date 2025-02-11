<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\TourType;
use App\Models\Country;
use App\Models\City;
use App\Models\Tour;

class TourController extends Controller
{
    public function __construct(private TourType $tour_types, 
    private Country $countries, private City $cities, private Tour $tour){}

    public function view(Request $request){
        // /agent/tour
        $tour_types = $this->tour_types
        ->get();
        $countries = $this->countries
        ->get();
        $cities = $this->cities
        ->get();
        $tour = $this->tour
        ->with(['destinations' => function($query){
            $query->with('city', 'country');
        }, 'availability', 'cancelation_items',
        'excludes', 'includes', 'itinerary', 'tour_type', 'pick_up_country',
        'pick_up_city'])
        ->get();   
    
        return response()->json([
            'tour_types' => $tour_types,
            'countries' => $countries,
            'cities' => $cities,
            'tour' => $tour
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
