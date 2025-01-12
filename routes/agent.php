<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AgentAuthController;

use App\Http\Controllers\Api\Agent\lead\LeadController;
use App\Http\Controllers\Api\Agent\customer\CustomerController;
use App\Http\Controllers\Api\Agent\supplier\SupplierController;

use App\Http\Controllers\Api\Agent\department\DepartmentController;

use App\Http\Controllers\Api\Agent\accounting\Wallet\WalletController;
use App\Http\Controllers\Api\Agent\accounting\financial\FinancialController;

use App\Http\Controllers\Api\Agent\manual_booking\ManualBookingController;

use App\Http\Controllers\Api\Agent\booking\BookingController;

use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomTypesController;
use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomAmenityController;
use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomExtraController;

use App\Http\Controllers\Api\Agent\settings\TaxController;
use App\Http\Controllers\Api\Agent\settings\CurrencyController;
use App\Http\Controllers\Api\Agent\settings\GroupController;

use App\Http\Controllers\Api\SuperAdmin\PaymentController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;

Route::controller(AgentAuthController::class)->group(function(){
    Route::get('signupLists', 'lists');
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
        Route::delete('delete/{id}', 'delete');
    });
    //marwan
    Route::controller(PlanController::class)->prefix('plan')->group(function(){
        Route::get('/', 'plans');
    });
    //marwan
    Route::controller(PaymentController::class)->prefix('payment')->group(function(){
        Route::get('/payment_methods', 'getPaymentMethods');
        Route::post('/make_payment', 'makePayment');
    });

    Route::controller(SupplierController::class)->prefix('supplier')->group(function(){
        Route::get('/', 'view');
        Route::get('item/{id}', 'supplier');
        Route::post('add', 'create');
        Route::post('update/{id}', 'modify');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(FinancialController::class)->prefix('financial')->group(function(){
        Route::get('/', 'view');
        Route::get('item/{id}', 'financial');
        Route::put('status/{id}', 'status');
        Route::post('add', 'create');
        Route::post('update/{id}', 'modify');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(WalletController::class)->prefix('wallet')->group(function(){
        Route::get('/', 'view');
        Route::post('add', 'add');
        Route::post('add', 'add');
        Route::post('charge', 'charge');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(ManualBookingController::class)->prefix('manual_booking')->group(function(){
        Route::post('/', 'booking');
        Route::get('/cart_data/{id}', 'cart_data');
        Route::post('/cart', 'cart');
        Route::get('/supplier_customer', 'to_b2_filter');
        Route::get('/service_supplier', 'from_supplier');
        Route::get('/taxes', 'from_taxes');
        Route::get('/lists', 'lists');
    });

    Route::controller(BookingController::class)->prefix('booking')->group(function(){
        Route::get('/upcoming', 'upcoming');
        Route::get('/current', 'current');
        Route::get('/past', 'past');
    });

    Route::controller(DepartmentController::class)->prefix('department')->group(function(){
        Route::get('/', 'view');
    });

    Route::controller(CustomerController::class)->prefix('customer')->group(function(){
        Route::get('/', 'view');
    });

    Route::prefix('/room')->group(function(){
        Route::prefix('/settings')->group(function(){
            Route::prefix('/types')->controller(RoomTypesController::class)
            ->group(function(){
                Route::get('/', 'view');
                Route::get('item/{id}', 'room_type');
                Route::put('status/{id}', 'status');
                Route::post('add', 'create');
                Route::post('update/{id}', 'modify');
                Route::delete('delete/{id}', 'delete');
            });
            
            Route::prefix('/amenity')->controller(RoomAmenityController::class)
            ->group(function(){
                Route::get('/', 'view');
                Route::get('item/{id}', 'room_amenity');
                Route::put('status/{id}', 'status');
                Route::post('add', 'create');
                Route::post('update/{id}', 'modify');
                Route::delete('delete/{id}', 'delete');
            });
            
            Route::prefix('/extra')->controller(RoomExtraController::class)
            ->group(function(){
                Route::get('/', 'view');
                Route::get('item/{id}', 'room_extra');
                Route::put('status/{id}', 'status');
                Route::post('add', 'create');
                Route::post('update/{id}', 'modify');
                Route::delete('delete/{id}', 'delete');
            });
        });
    });

    Route::prefix('/settings')->group(function(){
        Route::controller(TaxController::class)->prefix('tax')->group(function(){
            Route::get('/', 'view');
            Route::post('add', 'create');
            Route::post('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(CurrencyController::class)->prefix('currency')->group(function(){
            Route::get('/', 'view');
            Route::post('add', 'create');
            Route::post('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });

        Route::controller(GroupController::class)->prefix('group')->group(function(){
            Route::get('/', 'view');
            Route::get('item/{id}', 'group');
            Route::post('add', 'create');
            Route::post('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
