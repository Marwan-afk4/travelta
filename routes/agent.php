<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AgentAuthController;

use App\Http\Controllers\Api\Agent\lead\LeadController;
use App\Http\Controllers\Api\Agent\lead\LeadProfileController;

use App\Http\Controllers\Api\Agent\customer\CustomerController;
use App\Http\Controllers\Api\Agent\customer\CustomerProfileController;

use App\Http\Controllers\Api\Agent\supplier\SupplierController;
use App\Http\Controllers\Api\Agent\supplier\SupplierProfileController;

use App\Http\Controllers\Api\Agent\department\DepartmentController;

use App\Http\Controllers\Api\Agent\accounting\booking_payment\BookingPaymentController;

use App\Http\Controllers\Api\Agent\accounting_methods\Wallet\WalletController;
use App\Http\Controllers\Api\Agent\accounting_methods\financial\FinancialController;

use App\Http\Controllers\Api\Agent\manual_booking\ManualBookingController;

use App\Http\Controllers\Api\Agent\booking\BookingController;
use App\Http\Controllers\Api\Agent\booking\BookingStatusController;

use App\Http\Controllers\Api\Agent\Request\CreateRequestController;
use App\Http\Controllers\Api\Agent\Request\RequestListsController;

use App\Http\Controllers\Api\Agent\inventory\room\room\RoomGalleryController;
use App\Http\Controllers\Api\Agent\inventory\room\room\RoomController;
use App\Http\Controllers\Api\Agent\inventory\room\room\CreateRoomController;
use App\Http\Controllers\Api\Agent\inventory\room\room\RoomPricingController;
use App\Http\Controllers\Api\Agent\inventory\room\room\RoomAvailabilityController;

use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomTypesController;
use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomAmenityController;
use App\Http\Controllers\Api\Agent\inventory\room\settings\RoomExtraController;

use App\Http\Controllers\Api\Agent\inventory\tour\tour\TourController;
use App\Http\Controllers\Api\Agent\inventory\tour\tour\CreateTourController;

use App\Http\Controllers\Api\Agent\invoice\InvoiceController;

use App\Http\Controllers\Api\Agent\settings\TaxController;
use App\Http\Controllers\Api\Agent\settings\CurrencyController;
use App\Http\Controllers\Api\Agent\settings\GroupController;
use App\Http\Controllers\Api\SuperAdmin\BookingEngine;
use App\Http\Controllers\Api\SuperAdmin\PaymentController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;

Route::controller(AgentAuthController::class)->group(function(){
    Route::get('signupLists', 'lists');
    Route::post('signupAffilate', 'signup_affilate');
    Route::post('signupAgent', 'signup_agent');
    Route::post('login', 'login');
});


Route::middleware(['auth:sanctum','IsAgent'])->group(function () {
    Route::prefix('leads')->group(function(){
        Route::controller(LeadController::class)->group(function(){
            Route::get('/', 'view');
            Route::get('leads_search', 'leads_search');
            Route::put('update/{id}', 'modify');
            Route::post('add_lead', 'add_lead');
            Route::post('add', 'create');
            Route::delete('delete/{id}', 'delete');
        });

        Route::controller(LeadProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile');
        });
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
///////marwan start
    Route::post('/agent/bookingEngine', [BookingEngine::class, 'bookRoom']);

    Route::post('/agent/avalibleRooms', [BookingEngine::class, 'getAvailableRooms']);

    Route::get('/gethotels', [BookingEngine::class, 'getHotels']);

    Route::get('/getcities', [BookingEngine::class, 'getCities']);

    Route::get('/getcountries', [BookingEngine::class, 'getCountries']);
///////marwan end
    Route::prefix('supplier')->group(function(){
        Route::controller(SupplierController::class)->group(function(){
            Route::get('/', 'view');
            Route::get('item/{id}', 'supplier');
            Route::post('add', 'create');
            Route::post('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });

        Route::controller(SupplierProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile');
        });
    });

    Route::prefix('invoice')->group(function(){
        Route::controller(InvoiceController::class)->group(function(){
            Route::get('/', 'invoice');
        });
    });
    
    Route::prefix('accounting')->group(function(){
        Route::controller(BookingPaymentController::class)->prefix('booking')->group(function(){
            Route::post('/search', 'search');
            Route::post('/payment', 'add_payment');
            Route::get('/invoice/{id}', 'invoice');
        });
    });

    Route::prefix('request')->group(function(){
        Route::controller(RequestListsController::class)->group(function(){
            Route::get('/lists', 'lists');
            Route::get('/', 'view');
            Route::get('/stages_data', 'stages');
            Route::get('/item/{id}', 'request_item');
        });
        Route::controller(CreateRequestController::class)->group(function(){
            Route::post('/add_hotel', 'add_hotel');
            Route::post('/add_bus', 'add_bus');
            Route::post('/add_visa', 'add_visa');
            Route::post('/add_flight', 'add_flight');
            Route::post('/add_tour', 'add_tour');
            Route::put('/priority/{id}', 'priority');
            Route::put('/stages/{id}', 'stages');
            Route::put('/notes/{id}', 'notes');
            Route::delete('/delete/{id}', 'delete');
        });
    });

    Route::controller(FinancialController::class)->prefix('financial')->group(function(){
        Route::get('/', 'view');
        Route::get('item/{id}', 'financial');
        Route::post('transfer', 'transfer');
        Route::put('status/{id}', 'status');
        Route::post('add', 'create');
        Route::post('update/{id}', 'modify');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(WalletController::class)->prefix('wallet')->group(function(){
        Route::get('/', 'view');
        Route::get('/item/{id}', 'wallet');
        Route::post('add', 'add');
        Route::post('charge', 'charge');
        Route::delete('delete/{id}', 'delete');
    });

    Route::controller(ManualBookingController::class)->prefix('manual_booking')->group(function(){
        Route::post('/', 'booking');
        Route::get('/mobile_lists', 'mobile_lists');
        Route::get('/items', 'manuel_bookings');
        Route::delete('/cart/delete/{id}', 'delete_cart');
        Route::get('/cart_data/{id}', 'cart_data');
        Route::post('/cart', 'cart');
        Route::get('/supplier_customer', 'to_b2_filter');
        Route::post('/service_supplier', 'from_supplier');
        Route::post('/taxes', 'from_taxes');
        Route::get('/lists', 'lists');
    });

    Route::prefix('booking')->group(function(){
        Route::controller(BookingController::class)->group(function(){
            Route::get('/', 'booking');
            Route::get('/details/{id}', 'details');
            Route::put('/special_request/{id}', 'special_request');
        });
        Route::controller(BookingStatusController::class)->group(function(){
            Route::put('/confirmed/{id}', 'confirmed');
            Route::put('/vouchered/{id}', 'vouchered');
            Route::put('/canceled/{id}', 'canceled');
        });
    });

    Route::controller(DepartmentController::class)->prefix('department')->group(function(){
        Route::get('/', 'view');
    });

    Route::prefix('customer')->group(function(){
        Route::controller(CustomerController::class)->group(function(){
            Route::get('/', 'view');
        });
        Route::controller(CustomerProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile');
        });
    });

    Route::prefix('/tour')->group(function(){
        Route::controller(CreateTourController::class)->group(function(){
            Route::post('/add', 'create'); 
            Route::put('/update/{id}', 'modify'); 
            Route::delete('/delete/{id}', 'delete'); 
        });
    });

    Route::prefix('/room')->group(function(){
        Route::controller(RoomPricingController::class)
        ->prefix('/pricing')->group(function(){
            Route::get('/{id}', 'view');
            Route::get('/item/{id}', 'pricing');
            Route::put('/duplicate/{id}', 'duplicate');
            Route::post('/add', 'create');
            Route::post('/update/{id}', 'modify');
            Route::delete('/delete/{id}', 'delete');
        });
        Route::controller(RoomController::class)
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/room_list', 'room_list');
            Route::get('/lists', 'lists');
            Route::post('/hotel_lists', 'hotel_lists');
            Route::put('/duplicate_room/{id}', 'duplicate_room');
            Route::get('/item/{id}', 'room');
            Route::put('/status/{id}', 'status');
            Route::put('/accepted/{id}', 'accepted');
        });
        Route::controller(RoomAvailabilityController::class)
        ->prefix('/availability')->group(function(){
            Route::post('/', 'view');
            Route::get('item/{id}', 'room_availability');
            Route::post('add', 'create');
            Route::post('update', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(CreateRoomController::class)
        ->group(function(){
            Route::post('add', 'create');
            Route::post('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(RoomGalleryController::class)
        ->group(function(){
            Route::get('/gallery/{id}', 'gallery');
            Route::post('/add_gallery', 'add_gallery');
            Route::delete('/delete_gallery/{id}', 'delete');
        });

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
            Route::put('update/{id}', 'modify');
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
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
    });
});
