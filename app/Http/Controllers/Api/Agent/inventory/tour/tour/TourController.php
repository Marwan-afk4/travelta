<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TourType;
use App\Models\Country;
use App\Models\City;
use App\Models\Tour;

class TourController extends Controller
{
    public function __construct(private TourType $tour_types, 
    private Country $countries, private City $cities, private Tour $tour){}

    public function view(Request $request){
        $tour_types = $this->tour_types
        ->get();
        $countries = $this->countries
        ->get();
        $cities = $this->cities
        ->get();

        return response()->json([
            'tour_types' => $tour_types,
            'countries' => $countries,
            'cities' => $cities,
        ]);
    }
}
