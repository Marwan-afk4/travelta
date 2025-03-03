<?php

namespace App\Http\Controllers\Api\Agent\supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Http\Resources\ManuelBookingResource;
use App\Http\Resources\ManuelBusResource;
use App\Http\Resources\ManuelFlightResource;
use App\Http\Resources\ManuelHotelResource;
use App\Http\Resources\ManuelTourResource;
use App\Http\Resources\ManuelVisaResource;

use App\Models\SupplierAgent; 
use App\Models\ManuelBooking;
use App\Models\LegalPaper;
use App\Models\PaymentsCart;
use App\Models\AgentPayment;

class SupplierProfileController extends Controller
{
    public function __construct(
        private SupplierAgent $supplier,
        private ManuelBooking $manuel_booking, 
        private LegalPaper $legal_papers,
        private AgentPayment $agent_payment, 
        private PaymentsCart $payment_cart){}

    public function profile(Request $request, $id){
        // https://travelta.online/agent/supplier/profile/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $role = 'affilate_id';
        } 
        else {
            $role = 'agent_id';
        }
        $supplier_info = $this->supplier 
        ->where('id', $id)
        ->where($role, $agent_id)
        ->first();
        $manuel_booking = $this->manuel_booking
        ->with('from_supplier', 'country', 'hotel', 'bus',
        'flight', 'tour', 'visa')
        ->where('to_supplier_id', $id)
        ->where($role, $agent_id)
        ->get();
        $manuel_booking = ManuelBookingResource::collection($manuel_booking);
        $legal_papers = $this->legal_papers
        ->where('supplier_agent_id', $id)
        ->get();

        return response()->json([
            'supplier_info' => $supplier_info, 
            'manuel_booking' => $manuel_booking,
            'legal_papers' => $legal_papers,
            'balance' => $supplier_info->balance,
        ]);
    }

    public function transactions(Request $request ,$id){
        // agent/supplier/transactions/{id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }

        $hotel_current = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->where('check_in', '>', date('Y-m-d'));
        })  
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })
        ->get()
        ->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $bus_current = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        }) 
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $visa_current = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->where('travel_date', '>', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $flight_current = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->where('departure', '>', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $tour_current = $this->manuel_booking
        ->whereHas('tour.hotel')
        ->where($agent_type, $agent_id)
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_in', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $transactions_current = collect()->merge($hotel_current)->merge($bus_current)
        ->merge($visa_current)->merge($flight_current)->merge($tour_current);
        
        $t_hotel_past = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $t_bus_past = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('bus', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $t_visa_past = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('visa', function($query){
            $query->whereDate('travel_date', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $t_flight_past = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('flight', function($query){
            $query->whereDate('departure', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $t_tour_past = $this->manuel_booking
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel')
        ->whereDoesntHave('tour.hotel', function($query){
            $query->where('check_in', '>=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) use($id) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => $data->from_supplier_id == $id ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => $data->from_supplier_id == $id ? 'credit': 'debt',
            ];
        });
        $transactions_history = collect()->merge($t_hotel_past)->merge($t_bus_past)
        ->merge($t_visa_past)->merge($t_flight_past)->merge($t_tour_past); 
        
        $due_supplier = $this->payment_cart
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->where('status', 'approve')
        ->get();
        $due_from_supplier = $due_supplier->sum('due_payment');
        $due_from_agent = $this->manuel_booking
        ->where('from_supplier_id', $id)
        ->sum('cost');
        $due_from_agent -= $this->agent_payment
        ->where($agent_type, $agent_id)
        ->where('supplier_id', $id)
        ->sum('amount');
        $debt = 0;
        $credit = 0;
        if ($due_from_supplier > $due_from_agent) {
            $credit = $due_from_supplier - $due_from_agent;
        } else {
            $debt = $due_from_agent - $due_from_supplier;
        }
        $due = [
            'due_from_supplier' => $due_supplier->select('id', 'manuel_booking_id', 'amount', 'payment', 'due_payment', 'date'),
            'total_credit' => $credit,
            'total_debt' => $debt,
        ];
        
        return response()->json([
            'transactions_history_debt' => array_values($transactions_history->where('type', 'debt')->toArray()),
            'transactions_history_credit' => array_values($transactions_history->where('type', 'credit')->toArray()),
            'transactions_current_debt' => array_values($transactions_current->where('type', 'debt')->toArray()),
            'transactions_current_credit' => array_values($transactions_current->where('type', 'credit')->toArray()),
            'due' => $due
        ]);
    }
    
    public function transaction_details(Request $request ,$id){
        // agent/supplier/transaction_details/{manuel_booking_id}
        if ($request->user()->affilate_id && !empty($request->user()->affilate_id)) {
            $agent_id = $request->user()->affilate_id;
        }
        elseif ($request->user()->agent_id && !empty($request->user()->agent_id)) {
            $agent_id = $request->user()->agent_id;
        }
        else{
            $agent_id = $request->user()->id;
        }
        if ($request->user()->role == 'affilate' || $request->user()->role == 'freelancer') {    
            $agent_type = 'affilate_id';
        }
        else {
            $agent_type = 'agent_id';
        }
        $hotel = $this->manuel_booking
        ->with(['from_supplier', 'country', 'hotel'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel')
        ->where('id', $id)
        ->first();
        $bus = $this->manuel_booking
        ->with(['from_supplier', 'country', 'bus'])
        ->where($agent_type, $agent_id)
        ->where('id', $id)
        ->whereHas('bus')
        ->first();
        $visa = $this->manuel_booking
        ->with(['from_supplier', 'country', 'visa'])
        ->where($agent_type, $agent_id)
        ->whereHas('visa')
        ->where('id', $id)
        ->first();
        $flight = $this->manuel_booking
        ->with(['from_supplier', 'country', 'flight'])
        ->where($agent_type, $agent_id)
        ->whereHas('flight')
        ->where('id', $id)
        ->first();
        $tour = $this->manuel_booking
        ->with(['from_supplier', 'country', 
        'tour' => function($query){
            $query->with('hotel', 'bus');
        }])
        ->where($agent_type, $agent_id)
        ->whereHas('tour.hotel')
        ->where('id', $id)
        ->first(); 
        $hotel = empty($hotel) ? []: collect([$hotel]);
        $bus = empty($bus) ? []: collect([$bus]); 
        $visa = empty($visa) ? []: collect([$visa]); 
        $flight = empty($flight) ? []: collect([$flight]); 
        $tour = empty($tour) ? []: collect([$tour]);  
        
        $hotel = ManuelHotelResource::collection($hotel); 
        $bus = ManuelBusResource::collection($bus); 
        $visa = ManuelVisaResource::collection($visa); 
        $flight = ManuelFlightResource::collection($flight); 
        $tour = ManuelTourResource::collection($tour);

        return response()->json([
            'hotel' => $hotel[0] ?? null,
            'bus' => $bus[0] ?? null,
            'visa' => $visa[0] ?? null,
            'flight' => $flight[0] ?? null,
            'tour' => $tour[0] ?? null,
        ]);
    }
}
