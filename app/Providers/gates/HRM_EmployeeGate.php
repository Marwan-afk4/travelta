<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;


use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\AffilateAgent;
use App\Models\Agent;

class HRM_EmployeeGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_HRM_employee', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('HRM_employee') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_HRM_employee', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('HRM_employee') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('update_HRM_employee', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('HRM_employee') &&
            $user->user_positions->perimitions->pluck('action')->contains('update') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_HRM_employee', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('HRM_employee') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
    }
}
