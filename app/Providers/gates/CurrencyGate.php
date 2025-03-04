<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;


use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\AffilateAgent;
use App\Models\Agent;

class CurrencyGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_setting_currency', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('setting_currency') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_setting_currency', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('setting_currency') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('update_setting_currency', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('setting_currency') &&
            $user->user_positions->perimitions->pluck('action')->contains('update') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_setting_currency', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('setting_currency') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
    }
}
