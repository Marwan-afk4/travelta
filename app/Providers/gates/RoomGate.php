<?php

namespace App\Providers\gates;
use Illuminate\Support\Facades\Gate;


use App\Models\HrmEmployee;
use App\Models\AdminAgent;
use App\Models\AffilateAgent;
use App\Models\Agent;

class RoomGate
{
    public static function defineGates()
    {
        // if roles have booking payment module
        Gate::define('view_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('view') ) {
                return true;
            }
            return false;
        });
        Gate::define('duplicated_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('duplicated') ) {
                return true;
            }
            return false;
        });
        Gate::define('add_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('add') ) {
                return true;
            }
            return false;
        });
        Gate::define('availability_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('availability') ) {
                return true;
            }
            return false;
        });
        Gate::define('type_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('type') ) {
                return true;
            }
            return false;
        });
        Gate::define('amenity_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('amenity') ) {
                return true;
            }
            return false;
        });
        Gate::define('extra_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('extra') ) {
                return true;
            }
            return false;
        });
        Gate::define('update_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('update') ) {
                return true;
            }
            return false;
        });
        Gate::define('delete_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('delete') ) {
                return true;
            }
            return false;
        });
        Gate::define('gallary_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('gallary') ) {
                return true;
            }
            return false;
        });
        Gate::define('pricing_inventory_room', function ($user) {
            if ($user instanceof Agent || $user instanceof AffilateAgent) {
                return true;
            }
            if ($user->user_positions && 
            ($user instanceof AdminAgent || $user instanceof HrmEmployee) && 
            $user->user_positions->perimitions->pluck('module')->contains('inventory_room') &&
            $user->user_positions->perimitions->pluck('action')->contains('pricing') ) {
                return true;
            }
            return false;
        });
    }
}
