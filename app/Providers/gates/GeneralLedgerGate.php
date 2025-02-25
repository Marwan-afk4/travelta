<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\Agent;

class GeneralLedgerGate
{
    public static function defineGates()
    {
        // if roles have general_ledger module
        Gate::define('view_general_ledger', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('general_ledger') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_general_ledger', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('general_ledger') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('update_general_ledger', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('general_ledger') &&
            $user->user_positions->perimitions->pluck('action')->contains('update') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_general_ledger', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('general_ledger') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
    }
}
