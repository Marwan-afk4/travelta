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
        ->with('plan')
        ->get();
        $agent = $this->agents
        ->with('plan')
        ->get();
        $affilate = SubscriperResource::collection($affilate);
        $agent = SubscriperResource::collection($agent);
        $subscribers = $affilate->merge($agent);

        return response()->json([
            'subscribers' => $subscribers
        ]);
    }
}
