<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;


use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\AffilateAgent;
use App\Models\Agent;

class DepartmentGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_department', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('department') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
    }
}
