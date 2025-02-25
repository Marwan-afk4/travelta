<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;

use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\Agent;

class RevenueCategoryGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_supplier_payment_paid', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('supplier_payment_paid') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('view_supplier_payment_payable', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('supplier_payment_payable') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('view_supplier_payment_due', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('supplier_payment_due') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_supplier_payment_payable', function ($user) {
            if ($user instanceof Agent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('supplier_payment_payable') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('supplier_payment_due') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        }); 
    }
}
