<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TourType;
use App\Models\Country;
use App\Models\City;

class TourController extends Controller
{
    public function __construct(private TourType $tour_types, 
    private Country $countries, private City $cities){}

    public function view(Request $request){

    }
}
