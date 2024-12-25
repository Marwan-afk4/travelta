<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AffilateAgent;
use App\Models\Agent;
use Illuminate\Http\Request;

class SignupApproveController extends Controller
{


    public function getrequests(){
        $affilates = AffilateAgent::where('role', 'affilate')
        ->where('status', 'pending')
        ->get();
        $freelancers = AffilateAgent::where('role', 'freelancer')
        ->where('status', 'pending')
        ->get();
        $agency = Agent::where('role', 'agent')
        ->where('status', 'pending')
        ->get();
        $supplier = Agent::where('role', 'supplier')
        ->where('status', 'pending')
        ->get();

        return response()->json([
            'affilates' => $affilates,
            'freelancers' => $freelancers,
            'agency' => $agency,
            'supplier' => $supplier
        ]);
    }

    public function approveAgentSuplier($id){
        $agent = Agent::find($id);
        $agent->update([
            'status' => 'approve'
        ]);
        return response()->json([
            'message' => 'approved successfully'
        ]);
    }

    public function approveAffilate($id){
        $agent = AffilateAgent::find($id);
        $agent->update([
            'status' => 'approve'
        ]);
        return response()->json([
            'message' => 'approved successfully'
        ]);
    }

    public function rejectAgentSuplier($id){
        $agent = Agent::find($id);
        $agent->update([
            'status' => 'rejected'
        ]);
        return response()->json([
            'message' => 'rejected successfully'
        ]);
    }

    public function rejectAffilate($id){
        $agent = AffilateAgent::find($id);
        $agent->update([
            'status' => 'rejected'
        ]);
        return response()->json([
            'message' => 'rejected successfully'
        ]);
    }
}
