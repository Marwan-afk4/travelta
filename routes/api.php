<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use App\Http\Controllers\Api\SuperAdmin\AgencyController;
use App\Http\Controllers\Api\SuperAdmin\CityController;
use App\Http\Controllers\Api\SuperAdmin\CountryController;
use App\Http\Controllers\Api\SuperAdmin\CurrancyController;
use App\Http\Controllers\Api\SuperAdmin\DepartmentController;
use App\Http\Controllers\Api\SuperAdmin\HotelController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use App\Http\Controllers\Api\SuperAdmin\ServicesController;
use App\Http\Controllers\Api\SuperAdmin\UserController;
use App\Http\Controllers\Api\SuperAdmin\ZoneController;
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

/////////////////////////////////////////// Currancy ///////////////////////////////////////////////////

        Route::get('/super/currancy',[CurrancyController::class,'currancy']);

        Route::post('/super/currancy/add',[CurrancyController::class,'addCurrancy']);

        Route::delete('/super/currancy/delete/{id}',[CurrancyController::class,'deleteCurrancy']);

/////////////////////////////////////////// Department /////////////////////////////////////////////////

        Route::get('/super/departments',[DepartmentController::class,'departments']);

        Route::post('/super/department/add',[DepartmentController::class,'addDepartment']);

        Route::delete('/super/department/delete/{id}',[DepartmentController::class,'deleteDepartment']);

        Route::put('/super/department/update/{id}',[DepartmentController::class,'updateDepartment']);

//////////////////////////////////////////////// Hotels ///////////////////////////////////////////////////////////

        Route::get('/super/hotels', [HotelController::class, 'Hotels']);

        Route::post('/super/hotel/add', [HotelController::class, 'AddHotel']);

        Route::delete('/super/hotel/delete/{id}', [HotelController::class, 'DeleteHotel']);

        Route::put('/super/hotel/update/{id}',[HotelController::class,'UpdateHotel']);

//////////////////////////////////////////////// Services ///////////////////////////////////////////////////////////

        Route::get('/super/services', [ServicesController::class, 'services']);

        Route::post('/super/service/add', [ServicesController::class, 'addService']);

        Route::delete('/super/service/delete/{id}', [ServicesController::class, 'deleteService']);

//////////////////////////////////////////////// Country ///////////////////////////////////////////////////////////

        Route::get('/super/countries', [CountryController::class, 'getCountries']);

        Route::post('/super/country/add', [CountryController::class, 'addContry']);

        Route::delete('/super/country/delete/{id}', [CountryController::class, 'deleteCountry']);

        Route::put('/super/country/update/{id}',[CountryController::class,'updateCountry']);

//////////////////////////////////////////////// City ///////////////////////////////////////////////////////////

        Route::get('/super/cities', [CityController::class, 'getCity']);

        Route::post('/super/city/add', [CityController::class, 'addCity']);

        Route::delete('/super/city/delete/{id}', [CityController::class, 'deleteCity']);

        Route::put('/super/city/update/{id}',[CityController::class,'updateCity']);

//////////////////////////////////////////////// Zone ///////////////////////////////////////////////////////////

        Route::get('/super/zones', [ZoneController::class, 'getZone']);

        Route::post('/super/zone/add', [ZoneController::class, 'addZone']);

        Route::delete('/super/zone/delete/{id}', [ZoneController::class, 'deleteZone']);

        Route::put('/super/zone/update/{id}',[ZoneController::class,'updateZone']);

/////////////////////////////////////////////// Agent ///////////////////////////////////////////////////////////

        Route::get('/super/agents', [AgencyController::class, 'getAgency']);

        Route::delete('/super/agent/delete/{id}', [AgencyController::class, 'deleteAgency']);

        Route::put('/super/agent/update/{id}',[AgencyController::class,'updateAgency']);
});

