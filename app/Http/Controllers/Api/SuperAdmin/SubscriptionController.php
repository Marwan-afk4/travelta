<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriperResource;

use App\Models\AffilateAgent;
use App\Models\Agent;

class SubscriptionController extends Controller
{
    public function __construct(private AffilateAgent $affilates, private Agent $agents){}

    public function subscribers(){
        $affilate = $this->affilates
        ->where('end_date', '>=', date('Y-m-d'))
        ->whereNotNull('plan_id')
        ->with('plan', 'to_customer', 'to_supplier')
        ->get();
        $agent = $this->agents
        ->with('plan', 'to_customer', 'to_supplier')
        ->where('end_date', '>=', date('Y-m-d'))
        ->whereNotNull('plan_id')
        ->get();
        $affilate = SubscriperResource::collection($affilate);
        $agent = SubscriperResource::collection($agent);
        $subscribers = $affilate->merge($agent);

        return response()->json([
            'subscribers' => $subscribers
        ]);
    }
}
