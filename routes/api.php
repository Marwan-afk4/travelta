<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use App\Http\Controllers\Api\SuperAdmin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


    Route::post('/register', [Authcontroller::class, 'register']);
    Route::post('/login', [Authcontroller::class, 'login']);

    Route::middleware(['auth:sanctum','IsSuperAdmin'])->group(function () {

        Route::get('/super/users',[UserController::class,'users']);

        Route::post('/super/user/add',[UserController::class,'adduser']);

        Route::delete('/super/user/delete/{id}',[UserController::class,'deleteuser']);

///////////////////////////////////////// Plans //////////////////////////////////////////////////

        Route::get('/super/plans',[PlanController::class,'plans']);

        Route::post('/super/plan/add',[PlanController::class,'addplan']);

        Route::delete('/super/plan/delete/{id}',[PlanController::class,'deletePlan']);
    });

