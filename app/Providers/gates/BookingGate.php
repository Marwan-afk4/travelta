<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\Agent;

class BookingGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_manuel_booking', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('manuel_booking') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('view_booking_engine', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('booking_engine') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('view_bookings', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('bookings') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('status_bookings', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('bookings') &&
            $user->user_positions->perimitions->pluck('action')->contains('status') ) {
                return true;
            }
            return false;
        });
    }
}
