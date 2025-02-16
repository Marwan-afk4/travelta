<?php

namespace App\Http\Controllers\Api\Agent\supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Http\Resources\ManuelBookingResource;

use App\Models\SupplierAgent; 
use App\Models\ManuelBooking;
use App\Models\LegalPaper;

class SupplierProfileController extends Controller
{
    public function __construct(private SupplierAgent $supplier,
    private ManuelBooking $manuel_booking, private LegalPaper $legal_papers){}

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
        ->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
            ];
        });
        $transactions_current = collect()->merge($hotel_current)->merge($bus_current)
        ->merge($visa_current)->merge($flight_current)->merge($tour_current);
        
        $t_hotel_past = $this->manuel_booking
        ->with([ 'payments', 'payments_cart'])
        ->where($agent_type, $agent_id)
        ->whereHas('hotel', function($query){
            $query->whereDate('check_in', '<=', date('Y-m-d'));
        })
        
        ->where(function ($query) use ($id) {
            $query->where('from_supplier_id', $id)
                  ->orWhere('to_supplier_id', $id);
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
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
        })->get()->map(function ($data) {
            return [
                'manuel_booking_id' => $data->id ?? null,
                'amount' => !empty($data->from_supplier_id) ? $data->cost : $data->total_price,
                'date' => $data->created_at->format('Y-m-d') ?? null,
                'type' => !empty($data->from_supplier_id) ? 'debt': 'credit',
            ];
        });
        $transactions_history = collect()->merge($t_hotel_past)->merge($t_bus_past)
        ->merge($t_visa_past)->merge($t_flight_past)->merge($t_tour_past); 
        
        return response()->json([
            'transactions_history' => $transactions_history,
            'transactions_current' => $transactions_current,
        ]);
    }
}
