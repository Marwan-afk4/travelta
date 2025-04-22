<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuelBooking extends Model
{
    protected $fillable = [
        'invoice',
        'voucher',
        'to_supplier_id',
        'to_customer_id',
        'from_supplier_id',
        'from_service_id',
        'agent_sales_id',
        'code',
        'cost',
        'price',
        'currency_id',
        'tax_type',
        'total_price',
        'country_id',
        'city_id',
        'mark_up',
        'payment_type',
        'mark_up_type',
        'affilate_id',
        'agent_id',
        'status',
        'special_request',
        'request_status'
    ];
    protected $appends = ['to_client', 'voucher_link'];

    public function getVoucherLinkAttribute(){
        return url('storage/' . $this->voucher);
    }

    public function tasks(){
        return $this->hasMany(BookingTask::class, 'manuel_booking_id');
    }

    public function affilate(){
        return $this->belongsTo(AffilateAgent::class, 'affilate_id');
    }

    public function agent(){
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'from_service_id');
    }

    public function operation_confirmed(){
        return $this->hasMany(OperationBookingConfirmed::class, 'manuel_booking_id');
    }

    public function operation_vouchered(){
        return $this->hasMany(OperationBookingVouchered::class, 'manuel_booking_id');
    }

    public function operation_canceled(){
        return $this->hasMany(OperationBookingCanceled::class, 'manuel_booking_id');
    }

    public function payments(){
        return $this->hasMany(BookingPayment::class, 'manuel_booking_id');
    }

    public function currency(){
        return $this->belongsTo(CurrencyAgent::class, 'currency_id');
    }

    public function manuel_cart(){
        return $this->hasMany(ManuelCart::class);
    }

    public function payments_cart(){
        return $this->hasMany(PaymentsCart::class);
    }

    public function taxes(){
        return $this->belongsToMany(Tax::class, 'manuel_taxes', 'manuel_id', 'tax_id');
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function agent_sales(){
        return $this->belongsTo(HrmEmployee::class, 'agent_sales_id');
    }

    public function from_supplier(){
        return $this->belongsTo(SupplierAgent::class, 'from_supplier_id');
    }

    public function to_supplier(){
        return $this->belongsTo(SupplierAgent::class, 'to_supplier_id');
    }

    public function to_customer(){
        return $this->belongsTo(Customer::class, 'to_customer_id');
    }

    public function getToClientAttribute(){
        if (!empty($this->attributes['to_supplier_id'])) {
            return $this->belongsTo(SupplierAgent::class, 'to_supplier_id')->first();
        }
        return $this->belongsTo(Customer::class, 'to_customer_id')->first();
    }

    public function hotel(){
        return $this->hasOne(ManuelHotel::class, 'manuel_booking_id');
    }

    public function bus(){
        return $this->hasOne(ManuelBus::class, 'manuel_booking_id');
    }

    public function flight(){
        return $this->hasOne(ManuelFlight::class, 'manuel_booking_id');
    }

    public function tour(){
        return $this->hasOne(ManuelTour::class, 'manuel_booking_id');
    }

    public function visa(){
        return $this->hasOne(ManuelVisa::class, 'manuel_booking_id');
    }

    public function adults(){
        return $this->hasMany(Adult::class, 'manuel_booking_id');
    }

    public function children(){
        return $this->hasMany(Child::class, 'manuel_booking_id');
    }
}
