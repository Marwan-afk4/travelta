<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\Agent;

class RequestGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('priority_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('priority') ) {
                return true;
            }
            return false;
        });
        Gate::define('stages_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('stages') ) {
                return true;
            }
            return false;
        });
        Gate::define('notes_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('notes') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_request', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('request') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
    }
}
