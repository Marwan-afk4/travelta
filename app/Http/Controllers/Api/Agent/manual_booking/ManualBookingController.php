<?php

namespace App\Http\Controllers\Api\Agent\manual_booking;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class ManualBookingController extends Controller
{
    public function __construct(private City $cities, private Country $contries){}

    public function lists(){
        $cities = $this->cities
        ->get();
        $contries = $this->contries
        ->get();

        return response()->json([
            'cities' => $cities,
            'contries' => $contries,
        ]);
    }
}
