<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HotelPolicy;
use Illuminate\Http\Request;

class HotelPoliciesController extends Controller
{


    public function getHotelPolicies($hotel_id){
        $policies = HotelPolicy::where('hotel_id', $hotel_id)->get();
        $data = [
            'hotel_policies' => $policies
        ];
        return response()->json($data);
    }

    public function deletePolicy($id){
        $policy = HotelPolicy::find($id);
        $policy->delete();
        return response()->json([
            'message' => 'Policy deleted successfully',
        ]);
    }
}
