<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'to_name' => $this->customer?->name ?? null, 
            'to_phone' => $this->customer?->phone ?? null,
            'agent' => $this->admin_agent?->name,
            'service' => 'Hotel',
            'revenue' => $this->expected_revenue,
            'priority' => $this->priority,
            'stages' => $this->stages,
            'notes' => $this->notes ?? null,
            'currecy' => $this->currency->name,
            
            'tour_name' => $this->tour->tour ?? null,
            'tour_type' => $this->tour->type ?? null , 
            'tour_hotels' => $this->tour->hotel->select('destination', 'hotel_name', 'room_type', 'check_in', 'check_out', 'nights') ,
            'tour_buses' => $this->tour->bus->select('transportation', 'seats') ,
            'no_adults' => $this->tour->adults ?? null,
            'no_children' => $this->tour->childreen ?? null,
        ];
    }
}
