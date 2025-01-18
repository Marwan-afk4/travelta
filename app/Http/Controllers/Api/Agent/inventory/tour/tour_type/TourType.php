<?php

namespace App\Http\Controllers\Api\Agent\inventory\tour\tour_type;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TourType As TourTypes;

class TourType extends Controller
{
    public function __construct(private TourTypes $tour_type){}

    public function view(){
        $tour_type = $this->tour_type
        ->get();

        return response()->json([
            'tour_type' => $tour_type
        ]);
    }
}
