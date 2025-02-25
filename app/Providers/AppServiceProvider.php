<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Providers\gates\BookingPaymentGate;
use App\Providers\gates\ExpensesGate;

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
        // , , general_ledger, OE, payment_receivable, revenue, 
        // supplier_payment, financial, wallet, admin, admin_position, manuel_booking, 
        // booking_engine, bookings, customer, department, inventory_room, inventory_tour, 
        // invoice, lead, request, setting_currency, setting_group, setting_tax, supplier, HRM
        BookingPaymentGate::defineGates();
        ExpensesGate::defineGates();
    }
}
