<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Providers\gates\BookingPaymentGate;
use App\Providers\gates\ExpensesCategoryGate;
use App\Providers\gates\ExpensesGate;
use App\Providers\gates\GeneralLedgerGate;
use App\Providers\gates\OE_ownerGate;
use App\Providers\gates\OE_transactionGate;
use App\Providers\gates\RevenueGate;
use App\Providers\gates\RevenueCategoryGate;
use App\Providers\gates\AdminGate;
use App\Providers\gates\FinancialGate;
use App\Providers\gates\PaymentReceivableGate;
use App\Providers\gates\PositionGate;
use App\Providers\gates\SupplierPaymentGate;
use App\Providers\gates\WalletGate;
use App\Providers\gates\BookingGate;
use App\Providers\gates\DepartmentGate;
use App\Providers\gates\CustomerGate;
use App\Providers\gates\RoomGate;
use App\Providers\gates\TourGate;
use App\Providers\gates\LeadGate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // /agent/request/priority/{id}
        // key
        // priority [Low, Normal, High]
        //ExpensesGate::defineGates();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // invoice, request, setting_currency, setting_group, setting_tax, supplier, HRM
        BookingPaymentGate::defineGates();
        ExpensesCategoryGate::defineGates();
        ExpensesGate::defineGates();
        GeneralLedgerGate::defineGates();
        OE_ownerGate::defineGates();
        OE_transactionGate::defineGates();
        RevenueGate::defineGates();
        RevenueCategoryGate::defineGates();
        AdminGate::defineGates();
        FinancialGate::defineGates();
        PaymentReceivableGate::defineGates();
        PositionGate::defineGates();
        SupplierPaymentGate::defineGates();
        WalletGate::defineGates();
        BookingGate::defineGates();
        DepartmentGate::defineGates();
        RoomGate::defineGates();
        TourGate::defineGates();
        CustomerGate::defineGates();
        LeadGate::defineGates();
    }
}
