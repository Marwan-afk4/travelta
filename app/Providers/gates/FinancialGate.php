<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\Agent;

class FinancialGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_financial', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('financial') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('transfer_financial', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('financial') &&
            $user->user_positions->perimitions->pluck('action')->contains('transfer') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_financial', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('financial') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('update_financial', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('financial') &&
            $user->user_positions->perimitions->pluck('action')->contains('update') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_financial', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('financial') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
    }
}
