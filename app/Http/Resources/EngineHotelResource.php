<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineHotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supplier_from_name' => $this->from_supplier->name ?? null,
            'supplier_from_email' => $this->from_supplier->email ?? null,
            'supplier_from_phone' => $this->from_supplier->phone ?? null,
            'country' => $this->country->name ?? null,
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_client->name ?? null,
            'to_role' => !empty($this->to_agent_id) ? 'Agent' : 'Customer',
            'to_email' => $this->to_client->email,
            'to_phone' => $this->to_client->phone,
            'hotel_name' => $this->hotel->hotel_name ?? null,
            'check_in' => $this->check_in ?? null,
            'check_out' => $this->check_out ?? null,
            'no_nights' => $this->no_of_nights ?? null,
            'room_type' => $this->room_type ?? null,
            'no_adults' => $this->no_of_adults ?? null,
            'no_children' => $this->no_of_children ?? null,  
            'created_at' => $this->created_at ?? null,  
            'code' => $this->code ?? null,  
            'payment_status' => $this->payment_status ?? null,
            'status' => $this->status ?? null, 
            'special_request' => $this->special_request ?? null,
            'action' => $this->room->agent_id == $this->agent_id ? true : false,
        ];
    }
}
