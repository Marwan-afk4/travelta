<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Setting;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allow_time = Setting::
        where('name', 'allow_time')
        ->first();
        if (empty($allow_time)) {
            $allow_time = 0;
        } 
        else {
            $allow_time = $allow_time->value;
            $allow_time = json_decode($allow_time);
            $allow_time = $allow_time->days;
        }
        $end_date = Carbon::parse(Auth::user()->end_date);
        $end_date = $end_date->addDays($allow_time);
        
        if ((Auth::user()->role == 'affilate' || Auth::user()->role == 'freelancer'
        || Auth::user()->role == 'agent' || Auth::user()->role == 'supplier') && 
        Auth::user()->status == 'approve') {
            if (!empty(Auth::user()->plan_id) && 
            Auth::user()->start_date <= date('Y-m-d') &&
            $end_date >= date('Y-m-d')) {
                return $next($request);
            }
            else{
                return response()->json(['error' => 'The plan must be renewed'], 400);
            }
        } else {
            return response()->json(['error' => 'Unauthorized ,you are not agent'], 401);
        }
    }
}
