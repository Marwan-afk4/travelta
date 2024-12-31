<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AffilateAgent;
use App\Models\Agent;
use Illuminate\Http\Request;

class SignupApproveController extends Controller
{


    public function getRequests() {
        $roles = ['affilate', 'freelancer'];
        $affilatesAndFreelancers = AffilateAgent::whereIn('role', $roles)
            ->where('status', 'pending')
            ->with('legal_papers')
            ->get()
            ->groupBy('role');

        $roles = ['agent', 'supplier'];
        $agenciesAndSuppliers = Agent::whereIn('role', $roles)
            ->where('status', 'pending')
            ->with('legal_papers')
            ->get()
            ->groupBy('role');

        return response()->json([
            'affilates' => $affilatesAndFreelancers->get('affilate', []),
            'freelancers' => $affilatesAndFreelancers->get('freelancer', []),
            'agency' => $agenciesAndSuppliers->get('agent', []),
            'supplier' => $agenciesAndSuppliers->get('supplier', [])
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
