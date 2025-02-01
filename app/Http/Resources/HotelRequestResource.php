<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelRequestResource extends JsonResource
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
            'to_name' => $this->customer?->name ?? null, 
            'to_phone' => $this->customer?->phone ?? null,
            'agent' => $this->admin_agent?->name,
            'service' => 'Hotel',
            'revenue' => $this->expected_revenue,
            'priority' => $this->priority,
            'stages' => $this->stages,
            'currecy' => $this->currency->name,

            'hotel_name' => $this->hotel->hotel_name ?? null,
            'check_in' => $this->hotel->check_in ?? null,
            'check_out' => $this->hotel->check_out ?? null,
            'no_nights' => $this->hotel->nights ?? null,
            'room_type' => $this->hotel->room_type ?? null,
            'no_adults' => $this->hotel->adults ?? null,
            'no_children' => $this->hotel->childreen ?? null,
        ];
    }
}
