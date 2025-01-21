<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AffilateAgent;
use Illuminate\Http\Request;

class AffilateController extends Controller
{


    public function getAffilate(){
        $affilate = AffilateAgent::where('role', 'affilate')
        ->with(['legal_papers','plan'])
        ->whereHas('plan', function ($plan) {
            $plan->where('type', 'affiliate');
        })
        ->get();

        return response()->json(['affilate' => $affilate]);
    }

    public function getFreelancer(){
        $freelancer = AffilateAgent::where('role', 'freelancer')
        ->with(['legal_papers','plan'])
        ->whereHas('plan', function ($plan) {
            $plan->where('type', 'freelancer');
        })
        ->get();

        return response()->json(['freelancer' => $freelancer]);
    }

    public function deleteAffilateFreelance($id){
        $affilate = AffilateAgent::find($id);
        $affilate->delete();
        return response()->json(['message' => 'affilate deleted successfully']);
    }
}
