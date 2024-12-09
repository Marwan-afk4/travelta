<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AgentAuthController;

Route::controller(AgentAuthController::class)->group(function(){
    Route::post('login', 'login');
});