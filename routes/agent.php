<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AgentAuthController;
use App\Http\Controllers\Api\Agent\lead\LeadController;

Route::controller(AgentAuthController::class)->group(function(){
    Route::post('signupAffilate', 'signup_affilate');
    Route::post('signupAgent', 'signup_agent');
    Route::post('login', 'login');
});


Route::middleware(['auth:sanctum','IsAgent'])->group(function () {
    Route::controller(LeadController::class)->prefix('leads')->group(function(){
        Route::get('/', 'view');
        Route::get('leads_search', 'leads_search');
        Route::post('add_lead', 'add_lead');
        Route::post('add', 'create');
        Route::delete('delete', 'delete');
    });
});