<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class AgencyController extends Controller
{

//get agent
//============
    public function getAgency()
{
    $agents = Agent::with(['legal_papers', 'country', 'city', 'zone', 'plan'])
        ->whereHas('plan', function ($plan) {
            $plan->where('type', 'agency');
        })
        ->get();

    $data = $agents->map(function ($agent) {
        return [
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'agent_email' => $agent->email,
            'agent_phone' => $agent->phone,
            'owner_name' => $agent->owner_name,
            'owner_phone' => $agent->owner_phone,
            'owner_email' => $agent->owner_email,
            'agent_address' => $agent->address,
            'total_bookking' => $agent->total_booking,
            'total_commision' => $agent->total_commission,
            'country_id' => $agent->country_id,
            'country_name' => $agent->country?->name,
            'city_id' => $agent->city_id,
            'city_name' => $agent->city?->name,
            'zone_id' => $agent->zone_id,
            'zone_name' => $agent->zone?->name,
            'status' => $agent->status,
            'legal_papers' => $agent->legal_papers->map(function ($paper) {
                return [
                    'image_id' => $paper->id,
                    'agent_id' => $paper->agent_id,
                    'image' => $paper->image,
                    'image_type' => $paper->type,
                ];
            }),
            'plans' => $agent->plan ? [
                'plan_name' => $agent->plan->name,
                'branch_limit' => $agent->plan->branch_limit,
                'user_limit' => $agent->plan->user_limit,
                'user_cost' => $agent->plan->admin_cost,
                'branch_cost' => $agent->plan->branch_cost,
                'module_type' => $agent->plan->module_type,
                'period_in_days' => $agent->plan->period_in_days,
                'discount_type' => $agent->plan->discount_type,
                'discount_value' => $agent->plan->discount_value,
                'plan_price_after_discount' => $agent->plan->price_after_discount,
            ] : null,
        ];
    });

    return response()->json([
        'agents' => $data
    ]);
}

//delete Agent
//================
    public function deleteAgency($id){
        $agent=Agent::find($id);
        $agent->delete();
        return response()->json([
            'message' => 'Agent deleted successfully',
        ]);
    }

//update agent
//==============
    public function updateAgency(Request $request, $id)
{
    // Validate the request
    $validatedData = $request->validate([
        'name' => 'string|max:255',
        'email' => 'email|max:255',
        'phone' => 'string|max:20',
        'owner_name' => 'string|max:255',
        'owner_phone' => 'string|max:20',
        'owner_email' => 'email|max:255',
        'plan_id' => 'exists:plans,id',
        'address' => 'string|max:500',
        'legal_papers' => 'array',
        'legal_papers.*.id' => 'nullable|exists:legal_papers,id',
        'legal_papers.*.image' => 'string',
        'legal_papers.*.type' => 'string',
    ]);


    // Find the agent by ID
    $agent = Agent::with(['legal_papers', 'plan'])->findOrFail($id);

    // Update the agent's main attributes
    $agent->update([
        'name' => $request->name ?? $agent->name,
        'email' => $request->email ?? $agent->email,
        'phone' => $request->phone ?? $agent->phone,
        'owner_name' => $request->owner_name ?? $agent->owner_name,
        'owner_phone' => $request->owner_phone ?? $agent->owner_phone,
        'owner_email' => $request->owner_email ?? $agent->owner_email,
        'address' => $request->address ?? $agent->address,
        'plan_id' => $request->plan_id ?? $agent->plan_id
    ]);

    // Update legal papers if provided
    if ($request->has('legal_papers')) {
        foreach ($request->legal_papers as $paperData) {
            if (isset($paperData['id'])) {
                // Update existing legal paper
                $legalPaper = $agent->legal_papers()->find($paperData['id']);
                if ($legalPaper) {
                    $legalPaper->update([
                        'image' => $paperData['image'] ?? $legalPaper->image,
                        'type' => $paperData['type'] ?? $legalPaper->type,
                    ]);
                }
            } else {
                // Add a new legal paper
                $agent->legal_papers()->create([
                    'image' => $paperData['image'],
                    'type' => $paperData['type'],
                ]);
            }
        }
    }

    return response()->json([
        'message' => 'Agency updated successfully.'
    ]);
}




}
