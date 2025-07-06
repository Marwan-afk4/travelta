<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\SubscriperResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\AffilateAgent;
use App\Models\Agent;
use App\Models\Plan;

class SubscriptionController extends Controller
{
    public function __construct(private AffilateAgent $affilates, 
    private Agent $agents, private Plan $plans){}

    public function view(){
        $affilate = $this->affilates
        ->whereNotNull('plan_id')
        ->where('end_date', '>=', date('Y-m-d'))
        ->with('plan')
        ->get();
        $agent = $this->agents
        ->with('plan') 
        ->where('end_date', '>=', date('Y-m-d'))
        ->whereNotNull('plan_id')
        ->get();
        $affilate = SubscriperResource::collection($affilate);
        $agent = SubscriperResource::collection($agent);
        $subscribers = $affilate->merge($agent);
        
        $affilate = $this->affilates
        ->whereNull('plan_id')
        ->orWhere('end_date', '<', date('Y-m-d'))
        ->with('plan')
        ->get();
        $agent = $this->agents
        ->with('plan') 
        ->where('end_date', '<', date('Y-m-d'))
        ->orWhereNull('plan_id')
        ->get();
        $affilate = SubscriperResource::collection($affilate);
        $agent = SubscriperResource::collection($agent);
        $un_subscriber = $affilate->merge($agent);
        $plans = $this->plans
        ->get();

        return response()->json([
            'subscribers' => $subscribers,
            'un_subscriber' => $un_subscriber,
            'plans' => $plans,
        ]);
    }

    public function create(Request $request){
        $validation = Validator::make($request->all(), [
            'user_id' => ['required'],
            'plan_id' => ['required','exists:plans,id'],
            'role' => ['required', 'in:affilate,freelancer,agent,supplier']
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

        $plan  = $this->plans
        ->where('id', $request->plan_id)
        ->first();
        $start_date = Carbon::now();
        $end_date = $start_date->copy()->addDays($plan->period_in_days);
        if ($request->role == 'affilate' || $request->role == 'freelancer') {
            $this->affilates
            ->where('id', $request->user_id)
            ->update([
                'plan_id' => $request->plan_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }
        else{
            $this->agents
            ->where('id', $request->user_id)
            ->update([
                'plan_id' => $request->plan_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }

        return response()->json([
            'success' => 'You add data success'
        ]);
    }

    public function modify(Request $request){
        $validation = Validator::make($request->all(), [
            'user_id' => ['required'],
            'plan_id' => ['required','exists:plans,id'],
            'role' => ['required', 'in:affilate,freelancer,agent,supplier']
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }

        $plan  = $this->plans
        ->where('id', $request->plan_id)
        ->first();
        if ($request->role == 'affilate' || $request->role == 'freelancer') {
            $user = $this->affilates
            ->where('id', $request->user_id)
            ->first();
      
        }
        else{
            $user = $this->agents
            ->where('id', $request->user_id)
            ->first();
        }

        $start_date = Carbon::parse($user->start_date);
        $end_date = $start_date->copy()->addDays($plan->period_in_days);
        $user->update([
            'plan_id' => $request->plan_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);

        return response()->json([
            'success' => 'You update data success'
        ]);
    }

    public function delete(Request $request, $id){
        $validation = Validator::make($request->all(), [
            'role' => ['required', 'in:affilate,freelancer,agent,supplier']
        ]);
        if($validation->fails()){
            return response()->json(['errors'=>$validation->errors()], 401);
        }
        
        if ($request->role == 'affilate' || $request->role == 'freelancer') {
            $user = $this->affilates
            ->where('id', $id)
            ->first();
      
        }
        else{
            $user = $this->agents
            ->where('id', $id)
            ->first();
        }
        $user->update([
            'start_date' => null,
            'end_date' => null,
        ]);

        return response()->json([
            'success' => 'You delete package success'
        ]);
    }
}
