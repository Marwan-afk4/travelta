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
        ->where('to_customer_id', $id)
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
}
