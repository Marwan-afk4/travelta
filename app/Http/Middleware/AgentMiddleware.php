<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ((Auth::user()->role == 'affilate' || Auth::user()->role == 'freelancer'
        || Auth::user()->role == 'agent' || Auth::user()->role == 'supplier') && 
        Auth::user()->status == 'approve') {
            return $next($request);
        } else {
            return response()->json(['error' => 'Unauthorized ,you are not agent'], 401);
        }
    }
}
