<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AgentAuthController;

use App\Http\Controllers\Api\Agent\admins\AdminController;
use App\Http\Controllers\Api\Agent\admins\PositionController;

use App\Http\Controllers\Api\Agent\lead\LeadController;
use App\Http\Controllers\Api\Agent\lead\LeadProfileController;

use App\Http\Controllers\Api\Agent\customer\CustomerController;
use App\Http\Controllers\Api\Agent\customer\CustomerProfileController;

use App\Http\Controllers\Api\Agent\supplier\SupplierController;
use App\Http\Controllers\Api\Agent\supplier\SupplierProfileController;

use App\Http\Controllers\Api\Agent\department\DepartmentController;

use App\Http\Controllers\Api\Agent\accounting\booking_payment\BookingPaymentController;
use App\Http\Controllers\Api\Agent\accounting\supplier_payment\SupplierPaymentController;
use App\Http\Controllers\Api\Agent\accounting\expenses\ExpensesCategoryController;
use App\Http\Controllers\Api\Agent\accounting\expenses\ExpensesController;
use App\Http\Controllers\Api\Agent\accounting\revenue\CategoryRevenueController;
use App\Http\Controllers\Api\Agent\accounting\revenue\RevenueController;
use App\Http\Controllers\Api\Agent\accounting\OE\OwnerTransactionController;
use App\Http\Controllers\Api\Agent\accounting\OE\OwnerController;
use App\Http\Controllers\Api\Agent\accounting\payment_receivable\PaymentReceivableController;
use App\Http\Controllers\Api\Agent\accounting\general_ledger\GeneralLedgerController;

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
use App\Http\Controllers\Api\Agent\inventory\tour\tour\TourGalleryController;

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
            Route::get('/transactions/{id}', 'transactions');
            Route::get('/transaction_details/{manuel_booking_id}', 'transaction_details');
        });
    });

    Route::prefix('admin')->group(function(){
        Route::controller(AdminController::class)->group(function(){
            Route::get('/', 'view');
            Route::put('/status/{id}', 'status');
            Route::get('/item/{id}', 'admin');
            Route::post('/add', 'create');
            Route::put('/update/{id}', 'modify');
            Route::delete('/delete/{id}', 'delete');
        });
    });

    Route::prefix('admin/position')->group(function(){
        Route::controller(PositionController::class)->group(function(){
            Route::get('/', 'view');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'position');
            Route::post('/add', 'create');
            Route::put('/update/{id}', 'modify');
            Route::delete('/delete/{id}', 'delete');
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
        Route::controller(SupplierPaymentController::class)->group(function(){
            Route::get('/paid_to_suppliers', 'paid_to_suppliers');
            Route::post('/paid_to_suppliers_filter', 'paid_to_suppliers_filter');
            
            Route::get('/payable_to_suppliers', 'payable_to_suppliers');
            Route::post('/payable_to_suppliers_filter', 'payable_to_suppliers_filter');
            
            Route::get('/due_to_suppliers', 'due_to_suppliers');
            Route::post('/due_to_suppliers_filter', 'due_to_suppliers_filter');

            Route::get('/transactions', 'transactions');
            Route::post('/transactions_payment', 'add_payment');
        });
        Route::controller(OwnerController::class)->prefix('owner')
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'owner');
            Route::post('add', 'create');
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(OwnerTransactionController::class)->prefix('owner')
        ->group(function(){
            Route::get('/transactions_list', 'transactions_list');
            Route::post('/transaction', 'transaction');
        });
        Route::controller(GeneralLedgerController::class)->prefix('ledger')
        ->group(function(){
            Route::get('/', 'view');
        });
        Route::controller(PaymentReceivableController::class)->prefix('payment_receivable')
        ->group(function(){
            Route::get('/', 'view'); 
            Route::post('/filter', 'filter'); 
        });
        Route::controller(ExpensesCategoryController::class)->prefix('expenses/category')
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/item/{id}', 'category');
            Route::post('add', 'create');
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(ExpensesController::class)->prefix('expenses')
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'category');
            Route::post('/filter', 'filter');
            Route::post('add', 'create');
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(CategoryRevenueController::class)->prefix('revenue/category')
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/item/{id}', 'category');
            Route::post('add', 'create');
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
        });
        Route::controller(RevenueController::class)->prefix('revenue')
        ->group(function(){
            Route::get('/', 'view');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'category');
            Route::post('/filter', 'filter');
            Route::post('add', 'create');
            Route::put('update/{id}', 'modify');
            Route::delete('delete/{id}', 'delete');
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
            Route::get('/engine_details/{id}', 'engine_details');
            Route::put('/special_request/{id}', 'special_request');
            Route::put('/engine_special_request/{id}', 'engine_special_request');
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
        Route::controller(TourController::class)->group(function(){
            Route::get('/', 'view');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'tour');
            Route::put('/status/{id}', 'status');
            Route::put('/accepted/{id}', 'accepted');
        });
        Route::controller(CreateTourController::class)->group(function(){
            Route::post('/add', 'create'); 
            Route::put('/update/{id}', 'modify'); 
            Route::delete('/delete/{id}', 'delete'); 
        });
        Route::controller(TourGalleryController::class)->group(function(){
            Route::get('/gallery/{id}', 'gallery'); 
            Route::post('/add_gallery', 'add_gallery'); 
            Route::delete('/gallery/delete/{id}', 'delete'); 
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
