<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EngineTourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = $this->date;
        $date = Carbon::parse($date);
        $date = $date->format('Y-m-d');
        return [
            'id' => $this->id,
            'supplier_from_name' => $this->from_supplier->name ?? null,
            'supplier_from_email' => $this->from_supplier->email ?? null,
            'supplier_from_phone' => $this->from_supplier->phone ?? null,
            'country' => $this->country->name ?? null,
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_name ?? null,
            'to_role' => $this->to_role,
            'to_email' => $this->to_email,
            'to_phone' => $this->to_phone,
            'hotel_name' => $this->to_hotel->hotel_name ?? null,
            
            'check_in' => $this->date ?? null,
            'check_out' => $this->check_out ?? null,
            'no_nights' => $this->no_of_nights ?? null,
            'tour' => $this->tour->name ?? null,
            'no_of_people' => $this->no_of_people ?? null,
            
            'created_at' => $this->created_at ?? null,  
            'code' => $this->code ?? null,  
            'payment_status' => $this->payment_status ?? null,
            'status' => $this->status ?? null, 
            'special_request' => $this->special_request ?? null,
            'action' => ($this->tour->agent_id == $this->agent_id && is_numeric($this->agent_id)) 
            || ($this->tour->affilate_id == $this->affilate_id && is_numeric($this->affilate_id))
            ? true : false,
        ];
    }
}
