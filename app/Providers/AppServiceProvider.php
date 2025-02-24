<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\AdminAgent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // if roles have home module
        Gate::define('isHome', function (AdminAgent $user) {
            if($user->user_positions && $user->user_positions->roles->pluck('module')->contains('Home')){
                return true;
            }
        });
    }
}
