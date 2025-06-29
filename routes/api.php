<?php

use App\Http\Controllers\Api\Auth\Authcontroller;
use App\Http\Controllers\Api\SuperAdmin\AcceptedCardsController;
use App\Http\Controllers\Api\SuperAdmin\AffilateController;
use App\Http\Controllers\Api\SuperAdmin\AgencyController;
use App\Http\Controllers\Api\SuperAdmin\BookingController;
use App\Http\Controllers\Api\SuperAdmin\BookingEngine;
use App\Http\Controllers\Api\SuperAdmin\CityController;
use App\Http\Controllers\Api\SuperAdmin\CountryController;
use App\Http\Controllers\Api\SuperAdmin\CurrancyController;
use App\Http\Controllers\Api\SuperAdmin\DepartmentController;
use App\Http\Controllers\Api\SuperAdmin\FacilitiesController;
use App\Http\Controllers\Api\SuperAdmin\FeaturesController;
use App\Http\Controllers\Api\SuperAdmin\HotelController;
use App\Http\Controllers\Api\SuperAdmin\HotelImageController;
use App\Http\Controllers\Api\SuperAdmin\HotelPoliciesController;
use App\Http\Controllers\Api\SuperAdmin\PaymentController;
use App\Http\Controllers\Api\SuperAdmin\PaymentMethodController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use App\Http\Controllers\Api\SuperAdmin\RechargeWalletController;
use App\Http\Controllers\Api\SuperAdmin\RoomDataController;
use App\Http\Controllers\Api\SuperAdmin\ServicesController;
use App\Http\Controllers\Api\SuperAdmin\SignupApproveController;
use App\Http\Controllers\Api\SuperAdmin\SubscriptionController;
use App\Http\Controllers\Api\SuperAdmin\ThemeController;
use App\Http\Controllers\Api\SuperAdmin\UserController;
use App\Http\Controllers\Api\SuperAdmin\ZoneController;
use App\Http\Controllers\Api\SuperAdmin\settings\AllowTimeController;
use App\Http\Controllers\Api\SuperAdmin\TourTypeController;
use App\Http\Controllers\Api\SuperAdmin\MealPlanController;
use App\Http\Controllers\Api\SuperAdmin\AdminController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [Authcontroller::class, 'register']);
Route::post('/login', [Authcontroller::class, 'login']);
Route::get('/logout', [Authcontroller::class, 'logout'])->middleware('auth:sanctum');

////////////////////////////////////////// Bookings Engine //////////////////////////////////////////////////

        // Route::post('/super/bookingEngine', [BookingEngine::class, 'bookroom']);

        // Route::post('/super/avalibleRooms', [BookingEngine::class, 'getAvailableRooms']);


        

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::middleware(['auth:sanctum','IsSuperAdmin'])->group(function () {

        Route::get('/my_profile', [Authcontroller::class, 'my_profile']);
        Route::post('/update_my_profile', [Authcontroller::class, 'update_my_profile']);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////

        Route::get('/super/admin',[AdminController::class,'view']);
        Route::get('/super/admin/item/{id}',[AdminController::class,'admin']);
        Route::post('/super/admin/add',[AdminController::class,'create']);
        Route::post('/super/admin/update/{id}',[AdminController::class,'modify']);
        Route::delete('/super/admin/delete/{id}',[AdminController::class,'delete']);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
        Route::get('/super/users',[UserController::class,'users']);

        Route::post('/super/user/add',[UserController::class,'adduser']);

        Route::delete('/super/user/delete/{id}',[UserController::class,'deleteuser']);

///////////////////////////////////////// Plans //////////////////////////////////////////////////

        Route::get('/super/meal_plan',[MealPlanController::class,'view']);
        
        Route::get('/super/meal_plan/item/{id}',[MealPlanController::class,'meal']);

        Route::post('/super/meal_plan/add',[MealPlanController::class,'create']);

        Route::put('/super/meal_plan/update/{id}',[MealPlanController::class,'modify']);

        Route::delete('/super/meal_plan/delete/{id}',[MealPlanController::class,'delete']);

///////////////////////////////////////// Booking //////////////////////////////////////////////////

        Route::get('/super/bookings',[BookingController::class,'getBookings']);

/////////////////////////////////////// Subscription ////////////////////////////////////////////////

Route::get('/super/subscribers',[SubscriptionController::class,'subscribers']);

///////////////////////////////////// Settings => Allow Time //////////////////////////////////////////////

        Route::get('/super/settings/allow_time',[AllowTimeController::class,'view']);
        Route::post('/super/settings/allow_time/update',[AllowTimeController::class,'modify']);

///////////////////////////////////////// Plans //////////////////////////////////////////////////

        Route::get('/super/plans',[PlanController::class,'plans']);

        Route::post('/super/plan/add',[PlanController::class,'addplan']);

        Route::put('/super/plan/update/{id}',[PlanController::class,'updatePlan']);

        Route::delete('/super/plan/delete/{id}',[PlanController::class,'deletePlan']);

/////////////////////////////////////////// Currancy ///////////////////////////////////////////////////

        Route::get('/super/currancy',[CurrancyController::class,'currancy']);

        Route::post('/super/currancy/add',[CurrancyController::class,'addCurrancy']);

        Route::delete('/super/currancy/delete/{id}',[CurrancyController::class,'deleteCurrancy']);

        Route::put('/super/currancy/update/{id}',[CurrancyController::class,'updateCurrancy']);

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

        Route::get('/super/zones', [ZoneController::class, 'getZones']);

        Route::post('/super/zone/add', [ZoneController::class, 'addZone']);

        Route::delete('/super/zone/delete/{id}', [ZoneController::class, 'deleteZone']);

        Route::put('/super/zone/update/{id}',[ZoneController::class,'updateZone']);

///////////////////////////////////////////////// Agent ///////////////////////////////////////////////////////////

        Route::get('/super/agents', [AgencyController::class, 'getAgency']);

        Route::get('/super/supliers', [AgencyController::class, 'getSupplier']);

        Route::delete('/super/agent/delete/{id}', [AgencyController::class, 'deleteAgency']);

        Route::put('/super/agent/update/{id}',[AgencyController::class,'updateAgency']);

///////////////////////////////////////////////// Affilate ///////////////////////////////////////////////////////////

        Route::get('/super/affilates', [AffilateController::class, 'getAffilate']);

        Route::get('/super/freelancers', [AffilateController::class, 'getFreelancer']);

        Route::delete('/super/affilateFreelance/delete/{id}', [AffilateController::class, 'deleteAffilateFreelance']);

////////////////////////////////////////////// Approve Signup Request ///////////////////////////////////////////////////

        Route::get('/super/signupLists', [SignupApproveController::class, 'getrequests']);

        Route::put('/super/agent/approve/{id}',[SignupApproveController::class,'approveAgentSuplier']);

        Route::put('/super/agent/reject/{id}',[SignupApproveController::class,'rejectAgentSuplier']);

        Route::put('/super/affilate/approve/{id}',[SignupApproveController::class,'approveAffilate']);

        Route::put('/super/affilate/reject/{id}',[SignupApproveController::class,'rejectAffilate']);

///////////////////////////////////////////////// Payment Method /////////////////////////////////////////////////////////

        Route::get('/super/paymentMethods', [PaymentMethodController::class, 'getPaymentMethods']);

        Route::post('/super/paymentMethod/add', [PaymentMethodController::class, 'addPaymentMethod']);

        Route::delete('/super/paymentMethod/delete/{id}', [PaymentMethodController::class, 'deletePaymentMethod']);

//////////////////////////////////////////////// Pending Payments ///////////////////////////////////////////////////////////

        Route::get('/super/pendingPayments', [PaymentController::class, 'getPyamnts']);

        Route::get('super/approvedPayments', [PaymentController::class, 'approvedPayment']);

        Route::put('/super/accept-payment/{plan_id}/{id}',[PaymentController::class,'acceptPayment']);

        Route::put('/super/payment/reject/{id}',[PaymentController::class,'rejectPayment']);

        Route::post('/super/make-payment',[PaymentController::class , 'makePayment']);

//////////////////////////////////////////////// Facilites ///////////////////////////////////////////////////////////

        Route::get('/super/FacilIties', [FacilitiesController::class, 'getAllFacilities']);

        Route::post('/super/Facility/aDd', [FacilitiesController::class, 'addFacility']);

        Route::delete('/super/Facility/deLete/{id}', [FacilitiesController::class, 'deleteFacility']);

        Route::put('/super/Facility/uPdate/{id}',[FacilitiesController::class,'updateFacility']);

//////////////////////////////////////////////// Themes ///////////////////////////////////////////////////////////

        Route::get('/super/THemes', [ThemeController::class, 'getAllTheme']);

        Route::post('/super/Theme/Add', [ThemeController::class, 'addTheme']);

        Route::delete('/super/Theme/Delete/{id}', [ThemeController::class, 'deleteTheme']);

        Route::put('/super/Theme/updaTe/{id}',[ThemeController::class,'updateTheme']);

//////////////////////////////////////////// Accepted Cards ///////////////////////////////////////////////////////////

        Route::get('/super/acceptedCards', [AcceptedCardsController::class, 'getAllCards']);

        Route::post('/super/acceptedCard/add', [AcceptedCardsController::class, 'addCard']);

        Route::delete('/super/acceptedCard/delete/{id}', [AcceptedCardsController::class, 'deleteCard']);

        Route::put('/super/acceptedCard/update/{id}',[AcceptedCardsController::class,'updateCard']);

/////////////////////////////////////////////// Features ///////////////////////////////////////////////////////////

        Route::get('/super/Features', [FeaturesController::class, 'getAllFeatures']);

        Route::post('/super/Feature/adD', [FeaturesController::class, 'addFeature']);

        Route::delete('/super/Feature/dElete/{id}', [FeaturesController::class, 'deleteFeature']);

        Route::put('/super/Feature/Update/{id}',[FeaturesController::class,'updateFeature']);

//////////////////////////////////////////// Hotel Images ///////////////////////////////////////////////////////////

        Route::get('/super/hOtelImages/{id}', [HotelImageController::class, 'getAllHotelImages']);

        Route::delete('/super/hotElImage/deLete/{id}', [HotelImageController::class, 'deleteHotelImage']);

////////////////////////////////////////////////// Hotel Policy ///////////////////////////////////////////////////////////

        Route::get('/super/hOtelPolicIes/{id}', [HotelPoliciesController::class, 'getHotelPolicies']);

        Route::delete('/super/hotelPolicY/deLetE/{id}', [HotelPoliciesController::class, 'deletePolicy']);

///////////////////////////////////////////////////// Hotels ///////////////////////////////////////////////////////////

        Route::get('/super/hotels', [HotelController::class, 'getHotel']);

        Route::post('/super/hotel/add', [HotelController::class, 'storeHotel']);

        Route::delete('/super/hotel/delete/{id}', [HotelController::class, 'deleteHotel']);

///////////////////////////////////////////////// TourType ////////////////////////////////////////////////////////

        Route::get('/super/getTourtype', [TourTypeController::class , 'getTourtype']);

        Route::post('/super/add/tourtype', [TourTypeController::class , 'addTourtype']);

        Route::put('/super/update/tourtype/{id}', [TourTypeController::class , 'updateTourtype']);

        Route::delete('/super/delete/tourtype/{id}', [TourTypeController::class , 'deleteTourtype']);

//////////////////////////////////////////////////// Room Data ///////////////////////////////////////////////////////////

        Route::get('/super/roomData', [RoomDataController::class, 'getRoomData']);

        Route::post('/super/roomData/add', [RoomDataController::class, 'addRoomData']);

        Route::delete('/super/roomData/delete/{id}', [RoomDataController::class, 'deleteRoomData']);

        Route::put('/super/roomData/update/{id}',[RoomDataController::class,'updateRoomData']);

/////////////////////////////////////////////////// Pending Recharge Wallet ///////////////////////////////////////////////////////////

        Route::get('/super/pendingRechargeWallet', [RechargeWalletController::class, 'getPendingWallets']);

        Route::put('/super/rechargeWallet/approve/{id}',[RechargeWalletController::class,'approveRechargeWallet']);

        Route::put('/super/rechargeWallet/reject/{id}',[RechargeWalletController::class,'rejectRechargeWallet']);

});

