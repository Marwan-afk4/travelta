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
use App\Http\Controllers\Api\Agent\booking\BookingUpdateController;
use App\Http\Controllers\Api\Agent\booking\BookingStatusController;
use App\Http\Controllers\Api\Agent\booking\ConfirmationTaskController;

use App\Http\Controllers\Api\Agent\HRM\HRMagentController;
use App\Http\Controllers\Api\Agent\HRM\HRMdepartmentController;
use App\Http\Controllers\Api\Agent\HRM\HRMemployeeController;

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

use App\Http\Controllers\Api\Agent\Profile\ProfileController;

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
    Route::post('code', 'code');
    Route::post('login', 'login');
});

    //marwan
    Route::controller(PlanController::class)->prefix('plan')->group(function(){
        Route::get('/', 'plans')->middleware(['auth:sanctum']);
    });
    //marwan
    Route::controller(PaymentController::class)->prefix('payment')->group(function(){
        Route::get('/payment_methods', 'getPaymentMethods')->middleware(['auth:sanctum']);
        Route::post('/make_payment', 'makePayment')->middleware(['auth:sanctum']);
    });
///////marwan start

Route::middleware(['auth:sanctum','IsAgent'])->group(function () {
    Route::prefix('leads')->group(function(){
        Route::controller(LeadController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_lead');
            Route::get('/lists', 'lists');
            Route::get('/item/{id}', 'lead');
            Route::get('export_excel', 'export_excel')->middleware('can:add_lead');
            Route::post('import_excel', 'import_excel')->middleware('can:add_lead');
            Route::get('leads_search', 'leads_search')->middleware('can:add_lead');
            Route::post('update/{id}', 'modify')->middleware('can:update_lead');
            Route::put('status/{id}', 'status')->middleware('can:update_lead');
            Route::post('add_lead', 'add_lead')->middleware('can:add_lead');
            Route::post('add', 'create')->middleware('can:add_lead');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_lead');
        });

        Route::controller(LeadProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile')->middleware('can:update_lead');
        });
    });

    Route::controller(ProfileController::class)->group(function(){
        Route::get('/my_profile', 'my_profile');
        Route::post('/update_profile', 'update_profile');
    });

    Route::post('/agent/bookingEngine', [BookingEngine::class, 'bookRoom'])->middleware('can:view_booking_engine');

    Route::post('/agent/avalibleRooms', [BookingEngine::class, 'getAvailableRooms'])->middleware('can:view_booking_engine');

    Route::get('/gethotels', [BookingEngine::class, 'getHotels'])->middleware('can:view_booking_engine');

    Route::get('/getCustomers', [BookingEngine::class, 'getCustomers'])->middleware('can:view_booking_engine');

    Route::get('/getagents',[BookingEngine::class, 'getAgents'])->middleware('can:view_booking_engine');

    Route::get('/getNationalties', [BookingEngine::class, 'getNationalities'])->middleware('can:view_booking_engine');

    Route::get('/getcities', [BookingEngine::class, 'getCities'])->middleware('can:view_booking_engine');

    Route::get('/getcountries', [BookingEngine::class, 'getCountries'])->middleware('can:view_booking_engine');

    Route::get('/gettourtypes', [BookingEngine::class, 'getTourType'])->middleware('can:view_booking_engine');

    Route::get('/engine_payment', [BookingEngine::class, 'engine_payment'])->middleware('can:view_booking_engine');

    /////////////////////// Tours ///////////////////////

    Route::post('/agent/tours', [BookingEngine::class, 'getAvailableTours'])->middleware('can:view_booking_engine');

    Route::post('/agent/bookTour', [BookingEngine::class, 'bookTour'])->middleware('can:view_booking_engine');


///////marwan end
    Route::prefix('supplier')->group(function(){
        Route::controller(SupplierController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_supplier');
            Route::get('item/{id}', 'supplier')->middleware('can:view_supplier');
            Route::post('import_excel', 'import_excel')->middleware('can:add_supplier');
            Route::post('add', 'create')->middleware('can:add_supplier');
            Route::post('update/{id}', 'modify')->middleware('can:update_supplier');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_supplier');
        });

        Route::controller(SupplierProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile')->middleware('can:view_supplier');
            Route::get('/transactions/{id}', 'transactions')->middleware('can:view_supplier');
            Route::get('/transaction_details/{manuel_booking_id}', 'transaction_details')->middleware('can:view_supplier');
            Route::post('/upload_papers', 'upload_papers')->middleware('can:update_supplier');
        });
    });

    Route::prefix('admin')->group(function(){
        Route::controller(AdminController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_admin');
            Route::put('/status/{id}', 'status')->middleware('can:update_admin');
            Route::get('/item/{id}', 'admin')->middleware('can:view_admin');
            Route::post('/add', 'create')->middleware('can:add_admin');
            Route::post('/update/{id}', 'modify')->middleware('can:update_admin');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_admin');
        });
    });

    Route::prefix('admin/position')->group(function(){
        Route::controller(PositionController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_admin_position');
            Route::get('/lists', 'lists')->middleware('can:view_admin_position');
            Route::get('/item/{id}', 'position')->middleware('can:view_admin_position');
            Route::post('/add', 'create')->middleware('can:add_admin_position');
            Route::post('/update/{id}', 'modify')->middleware('can:update_admin_position');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_admin_position');
        });
    });

    Route::prefix('hrm/agent')->group(function(){
        Route::controller(HRMagentController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_HRM_agent');
            Route::get('/item/{id}', 'agent')->middleware('can:view_HRM_agent');
            Route::post('/add', 'add')->middleware('can:add_HRM_agent');
            Route::post('/update/{id}', 'modify')->middleware('can:update_HRM_agent');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_HRM_agent');
        });
    });

    Route::prefix('hrm/employee')->group(function(){
        Route::controller(HRMemployeeController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_HRM_employee');
            Route::get('/item/{id}', 'employee')->middleware('can:view_HRM_employee');
            Route::put('/status/{id}', 'status')->middleware('can:update_HRM_employee');
            Route::post('/add', 'create')->middleware('can:add_HRM_employee');
            Route::post('/update/{id}', 'modify')->middleware('can:update_HRM_employee');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_HRM_employee');
        });
    });

    Route::prefix('hrm/department')->group(function(){
        Route::controller(HRMdepartmentController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_HRM_department');
            Route::get('/item/{id}', 'department')->middleware('can:view_HRM_department');
            Route::put('/status/{id}', 'status')->middleware('can:update_HRM_department');
            Route::post('/add', 'create')->middleware('can:add_HRM_department');
            Route::post('/update/{id}', 'modify')->middleware('can:update_HRM_department');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_HRM_department');
        });
    });

    Route::prefix('invoice')->group(function(){
        Route::controller(InvoiceController::class)->group(function(){
            Route::get('/', 'invoice');
        });
    });

    Route::prefix('accounting')->group(function(){
        Route::controller(BookingPaymentController::class)->prefix('booking')->group(function(){
            Route::post('/search', 'search')->middleware('can:view_booking_payment');
            Route::post('/payment', 'add_payment')->middleware('can:add_booking_payment');
            Route::get('/invoice/{id}', 'invoice')->middleware('can:view_booking_payment');
        });
        Route::controller(SupplierPaymentController::class)->group(function(){
            Route::get('/paid_to_suppliers', 'paid_to_suppliers')->middleware('can:view_supplier_payment_paid');
            Route::post('/paid_to_suppliers_filter', 'paid_to_suppliers_filter')->middleware('can:view_supplier_payment_paid');

            Route::get('/payable_to_suppliers', 'payable_to_suppliers')->middleware('can:view_supplier_payment_payable');
            Route::post('/payable_to_suppliers_filter', 'payable_to_suppliers_filter')->middleware('can:view_supplier_payment_payable');

            Route::get('/due_to_suppliers', 'due_to_suppliers')->middleware('can:view_supplier_payment_due');
            Route::post('/due_to_suppliers_filter', 'due_to_suppliers_filter')->middleware('can:view_supplier_payment_due');

            Route::get('/transactions', 'transactions')->middleware('can:add_supplier_payment_payable');
            Route::post('/transactions_payment', 'add_payment')->middleware('can:add_supplier_payment_payable');
        });
        Route::controller(OwnerController::class)->prefix('owner')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_OE_owner');
            Route::get('/lists', 'lists')->middleware('can:view_OE_owner');
            Route::get('/item/{id}', 'owner')->middleware('can:view_OE_owner');
            Route::post('add', 'create')->middleware('can:add_OE_owner');
            Route::post('update/{id}', 'modify')->middleware('can:update_OE_owner');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_OE_owner');
        });
        Route::controller(OwnerTransactionController::class)->prefix('owner')
        ->group(function(){
            Route::get('/transactions_list', 'transactions_list')->middleware('can:view_OE_transaction');
            Route::post('/transaction', 'transaction')->middleware('can:add_OE_transaction');
        });
        Route::controller(GeneralLedgerController::class)->prefix('ledger')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_general_ledger');
        });
        Route::controller(PaymentReceivableController::class)->prefix('payment_receivable')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_payment_receivable');
            Route::post('/filter', 'filter')->middleware('can:view_payment_receivable');
        });
        Route::controller(ExpensesCategoryController::class)->prefix('expenses/category')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_expenses_category');
            Route::get('/item/{id}', 'category')->middleware('can:view_expenses_category');
            Route::post('add', 'create')->middleware('can:add_expenses_category');
            Route::post('update/{id}', 'modify')->middleware('can:update_expenses_category');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_expenses_category');
        });
        Route::controller(ExpensesController::class)->prefix('expenses')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_expenses');
            Route::get('/lists', 'lists')->middleware('can:view_expenses');
            Route::get('/item/{id}', 'category')->middleware('can:view_expenses');
            Route::post('/filter', 'filter')->middleware('can:view_expenses');
            Route::post('add', 'create')->middleware('can:add_expenses');
            Route::post('update/{id}', 'modify')->middleware('can:update_expenses');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_expenses');
        });
        Route::controller(CategoryRevenueController::class)->prefix('revenue/category')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_revenue_category');
            Route::get('/item/{id}', 'category')->middleware('can:view_revenue_category');
            Route::post('add', 'create')->middleware('can:add_revenue_category');
            Route::post('update/{id}', 'modify')->middleware('can:update_revenue_category');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_revenue_category');
        });
        Route::controller(RevenueController::class)->prefix('revenue')
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_revenue');
            Route::get('/lists', 'lists')->middleware('can:view_revenue');
            Route::get('/item/{id}', 'category')->middleware('can:view_revenue');
            Route::post('/filter', 'filter')->middleware('can:view_revenue');
            Route::post('add', 'create')->middleware('can:add_revenue');
            Route::post('update/{id}', 'modify')->middleware('can:update_revenue');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_revenue');
        });
    });

    Route::prefix('request')->group(function(){
        Route::controller(RequestListsController::class)->group(function(){
            Route::get('/lists', 'lists')->middleware('can:view_request');
            Route::get('/', 'view')->middleware('can:view_request');
            Route::get('/stages_data', 'stages')->middleware('can:view_request');
            Route::get('/item/{id}', 'request_item')->middleware('can:view_request');
        });
        Route::controller(CreateRequestController::class)->group(function(){
            Route::post('/add_hotel', 'add_hotel')->middleware('can:add_request');
            Route::post('/add_bus', 'add_bus')->middleware('can:add_request');
            Route::post('/add_visa', 'add_visa')->middleware('can:add_request');
            Route::post('/add_flight', 'add_flight')->middleware('can:add_request');
            Route::post('/add_tour', 'add_tour')->middleware('can:add_request');
            
            Route::post('/update_hotel/{id}', 'update_hotel')->middleware('can:update_request');
            Route::post('/update_bus/{id}', 'update_bus')->middleware('can:update_request');
            Route::post('/update_visa/{id}', 'update_visa')->middleware('can:update_request');
            Route::post('/update_flight/{id}', 'update_flight')->middleware('can:update_request');
            Route::post('/update_tour/{id}', 'update_tour')->middleware('can:update_request');

            Route::put('/priority/{id}', 'priority')->middleware('can:priority_request');
            Route::put('/stages/{id}', 'stages')->middleware('can:stages_request');
            Route::put('/notes/{id}', 'notes')->middleware('can:notes_request');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_request');
        });
    });

    Route::controller(FinancialController::class)->prefix('financial')->group(function(){
        Route::get('/', 'view')->middleware('can:view_financial');
        Route::get('item/{id}', 'financial')->middleware('can:view_financial');
        Route::post('transfer', 'transfer')->middleware('can:transfer_financial');
        Route::put('status/{id}', 'status')->middleware('can:update_financial');
        Route::post('add', 'create')->middleware('can:add_financial');
        Route::post('update/{id}', 'modify')->middleware('can:update_financial');
        Route::delete('delete/{id}', 'delete')->middleware('can:delete_financial');
    });

    Route::controller(WalletController::class)->prefix('wallet')->group(function(){
        Route::get('/', 'view')->middleware('can:view_wallet');
        Route::get('/item/{id}', 'wallet')->middleware('can:view_wallet');
        Route::post('add', 'add')->middleware('can:add_wallet');
        Route::post('charge', 'charge')->middleware('can:charge_wallet');
        Route::delete('delete/{id}', 'delete')->middleware('can:delete_wallet');
    });

    Route::controller(ManualBookingController::class)
    ->prefix('manual_booking')->group(function(){
        Route::post('/', 'booking')->middleware('can:view_manuel_booking');
        Route::get('/mobile_lists', 'mobile_lists')->middleware('can:view_manuel_booking');
        Route::get('/items', 'manuel_bookings')->middleware('can:view_manuel_booking');
        Route::delete('/cart/delete/{id}', 'delete_cart')->middleware('can:view_manuel_booking');
        Route::get('/cart_data/{id}', 'cart_data')->middleware('can:view_manuel_booking');
        Route::put('/update_cart_data/{id}', 'update_cart_data')->middleware('can:view_manuel_booking');
        Route::post('/cart', 'cart')->middleware('can:view_manuel_booking');
        Route::get('/supplier_customer', 'to_b2_filter')->middleware('can:view_manuel_booking');
        Route::post('/service_supplier', 'from_supplier')->middleware('can:view_manuel_booking');
        Route::post('/taxes', 'from_taxes')->middleware('can:view_manuel_booking');
        Route::get('/lists', 'lists')->middleware('can:view_manuel_booking');
        Route::post('/pdf', 'pdf')->middleware('can:view_manuel_booking');
    });

    Route::prefix('booking')->group(function(){
        Route::controller(BookingController::class)->group(function(){
            Route::get('/', 'booking')->middleware('can:view_bookings');
            Route::get('/details/{id}', 'details')->middleware('can:view_bookings');
            Route::get('/booking_item/{id}', 'booking_item')->middleware('can:view_bookings');
            Route::get('/engine_tour_details/{id}', 'engine_tour_details')->middleware('can:view_bookings');
            Route::get('/engine_details/{id}', 'engine_details')->middleware('can:view_bookings');
            Route::post('/special_request/{id}', 'special_request')->middleware('can:view_bookings');
            Route::post('/request_status/{id}', 'special_request_status')->middleware('can:update_bookings');
            Route::post('/engine_special_request/{id}', 'engine_special_request')->middleware('can:update_bookings');
            Route::post('/request_status_engine/{id}', 'special_request_status_engine')->middleware('can:update_bookings');
            Route::post('/engine_tour_special_request/{id}', 'engine_tour_special_request')->middleware('can:update_bookings');
            Route::post('/special_status_tour_engine/{id}', 'special_status_tour_engine')->middleware('can:update_bookings');
        });
        Route::controller(BookingUpdateController::class)->group(function(){
            Route::get('/hotel/{id}', 'hotel')->middleware('can:update_bookings');
            Route::get('/flight/{id}', 'flight')->middleware('can:update_bookings');
            Route::get('/bus/{id}', 'bus')->middleware('can:update_bookings');
            Route::get('/visa/{id}', 'visa')->middleware('can:update_bookings');
            Route::get('/tour/{id}', 'tour')->middleware('can:update_bookings');
            
            Route::post('/update_hotel/{id}', 'update_hotel')->middleware('can:update_bookings');
            Route::post('/update_flight/{id}', 'update_flight')->middleware('can:update_bookings');
            Route::post('/update_bus/{id}', 'update_bus')->middleware('can:update_bookings');
            Route::post('/update_visa/{id}', 'update_visa')->middleware('can:update_bookings');
            Route::post('/update_tour/{id}', 'update_tour')->middleware('can:update_bookings');
        });
        Route::controller(BookingStatusController::class)->group(function(){
            Route::post('/confirmed/{id}', 'confirmed')->middleware('can:status_bookings');
            Route::post('/vouchered/{id}', 'vouchered')->middleware('can:status_bookings');
            Route::post('/canceled/{id}', 'canceled')->middleware('can:status_bookings');

            Route::post('/engine_confirmed/{id}', 'engine_confirmed')->middleware('can:status_bookings');
            Route::post('/engine_vouchered/{id}', 'engine_vouchered')->middleware('can:status_bookings');
            Route::post('/engine_canceled/{id}', 'engine_canceled')->middleware('can:status_bookings');

            Route::post('/engine_tour_confirmed/{id}', 'engine_tour_confirmed')->middleware('can:status_bookings');
            Route::post('/engine_tour_vouchered/{id}', 'engine_tour_vouchered')->middleware('can:status_bookings');
            Route::post('/engine_tour_canceled/{id}', 'engine_tour_canceled')->middleware('can:status_bookings');
        });
        Route::controller(ConfirmationTaskController::class)->prefix('task')->group(function(){
            Route::get('/manuel/{id}', 'manuel_tasks')->middleware('can:view_bookings');
            Route::get('/engine/{id}', 'engine_tasks')->middleware('can:view_bookings');
            Route::get('/tour_engine/{id}', 'engine_tour_tasks')->middleware('can:view_bookings');
            Route::get('/item/{id}', 'task')->middleware('can:view_bookings');
            Route::post('/add', 'create')->middleware('can:view_bookings');
            Route::post('/update/{id}', 'modify')->middleware('can:view_bookings');
            Route::delete('/delete/{id}', 'delete')->middleware('can:view_bookings');
        });
    });

    Route::controller(DepartmentController::class)->prefix('department')->group(function(){
        Route::get('/', 'view')->middleware('can:view_department');
    });

    Route::prefix('customer')->group(function(){
        Route::controller(CustomerController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_customer');
            Route::get('/request_item/{id}', 'request_item')->middleware('can:view_customer');
            Route::put('/status/{id}', 'status')->middleware('can:update_customer');
            Route::post('/update/{id}', 'update')->middleware('can:update_customer');
        });
        Route::controller(CustomerProfileController::class)->group(function(){
            Route::get('/profile/{id}', 'profile')->middleware('can:view_customer');
            Route::post('/upload_papers', 'upload_papers')->middleware('can:update_customer');
        });
    });

    Route::prefix('/tour')->group(function(){
        Route::controller(TourController::class)->group(function(){
            Route::get('/', 'view')->middleware('can:view_inventory_tour');
            Route::get('/lists', 'lists')->middleware('can:view_inventory_tour');
            Route::get('/item/{id}', 'tour')->middleware('can:view_inventory_tour');
            Route::put('/status/{id}', 'status')->middleware('can:update_inventory_tour');
            Route::put('/accepted/{id}', 'accepted')->middleware('can:update_inventory_tour');
        });
        Route::controller(CreateTourController::class)->group(function(){
            Route::post('/add', 'create')->middleware('can:add_inventory_tour');
            Route::post('/update/{id}', 'modify')->middleware('can:update_inventory_tour');
            Route::delete('/delete/{id}', 'delete')->middleware('can:delete_inventory_tour');
        });
        Route::controller(TourGalleryController::class)->group(function(){
            Route::get('/gallery/{id}', 'gallery')->middleware('can:gallary_inventory_tour');
            Route::post('/add_gallery', 'add_gallery')->middleware('can:gallary_inventory_tour');
            Route::delete('/gallery/delete/{id}', 'delete')->middleware('can:gallary_inventory_tour');
        });
    });

    Route::prefix('/room')->group(function(){
        Route::controller(RoomPricingController::class)
        ->prefix('/pricing')->group(function(){
            Route::get('/{id}', 'view')->middleware('can:pricing_inventory_room');
            Route::get('/item/{id}', 'pricing')->middleware('can:pricing_inventory_room');
            Route::put('/duplicate/{id}', 'duplicate')->middleware('can:pricing_inventory_room');
            Route::post('/add', 'create')->middleware('can:pricing_inventory_room');
            Route::post('/update/{id}', 'modify')->middleware('can:pricing_inventory_room');
            Route::delete('/delete/{id}', 'delete')->middleware('can:pricing_inventory_room');
        });
        Route::controller(RoomController::class)
        ->group(function(){
            Route::get('/', 'view')->middleware('can:view_inventory_room');
            Route::get('/room_list', 'room_list')->middleware('can:view_inventory_room');
            Route::get('/lists', 'lists')->middleware('can:view_inventory_room');
            Route::post('/hotel_lists', 'hotel_lists')->middleware('can:view_inventory_room');
            Route::put('/duplicate_room/{id}', 'duplicate_room')->middleware('can:duplicated_inventory_room');
            Route::get('/item/{id}', 'room')->middleware('can:update_inventory_room');
            Route::put('/status/{id}', 'status')->middleware('can:update_inventory_room');
            Route::put('/accepted/{id}', 'accepted')->middleware('can:update_inventory_room');
        });
        Route::controller(RoomAvailabilityController::class)
        ->prefix('/availability')->group(function(){
            Route::post('/', 'view')->middleware('can:availability_inventory_room');
            Route::get('item/{id}', 'room_availability')->middleware('can:availability_inventory_room');
            Route::post('add', 'create')->middleware('can:availability_inventory_room');
            Route::post('update', 'modify')->middleware('can:availability_inventory_room');
            Route::delete('delete/{id}', 'delete')->middleware('can:availability_inventory_room');
        });
        Route::controller(CreateRoomController::class)
        ->group(function(){
            Route::post('add', 'create')->middleware('can:add_inventory_room');
            Route::post('update/{id}', 'modify')->middleware('can:update_inventory_room');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_inventory_room');
        });
        Route::controller(RoomGalleryController::class)
        ->group(function(){
            Route::get('/gallery/{id}', 'gallery')->middleware('can:gallary_inventory_room');
            Route::post('/add_gallery', 'add_gallery')->middleware('can:gallary_inventory_room');
            Route::delete('/delete_gallery/{id}', 'delete')->middleware('can:gallary_inventory_room');
        });

        Route::prefix('/settings')->group(function(){
            Route::prefix('/types')->controller(RoomTypesController::class)
            ->group(function(){
                Route::get('/', 'view')->middleware('can:type_inventory_room');
                Route::get('item/{id}', 'room_type')->middleware('can:type_inventory_room');
                Route::put('status/{id}', 'status')->middleware('can:type_inventory_room');
                Route::post('add', 'create')->middleware('can:type_inventory_room');
                Route::post('update/{id}', 'modify')->middleware('can:type_inventory_room');
                Route::delete('delete/{id}', 'delete')->middleware('can:type_inventory_room');
            });

            Route::prefix('/amenity')->controller(RoomAmenityController::class)
            ->group(function(){
                Route::get('/', 'view')->middleware('can:amenity_inventory_room');
                Route::get('item/{id}', 'room_amenity')->middleware('can:amenity_inventory_room');
                Route::put('status/{id}', 'status')->middleware('can:amenity_inventory_room');
                Route::post('add', 'create')->middleware('can:amenity_inventory_room');
                Route::post('update/{id}', 'modify')->middleware('can:amenity_inventory_room');
                Route::delete('delete/{id}', 'delete')->middleware('can:amenity_inventory_room');
            });

            Route::prefix('/extra')->controller(RoomExtraController::class)
            ->group(function(){
                Route::get('/', 'view')->middleware('can:extra_inventory_room');
                Route::get('item/{id}', 'room_extra')->middleware('can:extra_inventory_room');
                Route::put('status/{id}', 'status')->middleware('can:extra_inventory_room');
                Route::post('add', 'create')->middleware('can:extra_inventory_room');
                Route::post('update/{id}', 'modify')->middleware('can:extra_inventory_room');
                Route::delete('delete/{id}', 'delete')->middleware('can:extra_inventory_room');
            });
        });
    });

    Route::prefix('/settings')->group(function(){
        Route::controller(TaxController::class)->prefix('tax')->group(function(){
            Route::get('/', 'view')->middleware('can:view_setting_tax');
            Route::get('item/{id}', 'tax')->middleware('can:view_setting_tax');
            Route::post('add', 'create')->middleware('can:add_setting_tax');
            Route::post('update/{id}', 'modify')->middleware('can:update_setting_tax');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_setting_tax');
        });
        Route::controller(CurrencyController::class)->prefix('currency')->group(function(){
            Route::get('/', 'view')->middleware('can:view_setting_currency');
            Route::get('/item/{id}', 'currency')->middleware('can:view_setting_currency');
            Route::post('add', 'create')->middleware('can:add_setting_currency');
            Route::post('update/{id}', 'modify')->middleware('can:update_setting_currency');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_setting_currency');
        });
        Route::controller(GroupController::class)->prefix('group')->group(function(){
            Route::get('/', 'view')->middleware('can:view_setting_group');
            Route::get('item/{id}', 'group')->middleware('can:view_setting_group');
            Route::post('add', 'create')->middleware('can:add_setting_group');
            Route::post('update/{id}', 'modify')->middleware('can:update_setting_group');
            Route::delete('delete/{id}', 'delete')->middleware('can:delete_setting_group');
        });
    });
});
