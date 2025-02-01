<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisaRequestResource extends JsonResource
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
            'notes' => $this->notes ?? null,
            'currecy' => $this->currency->name,
            
            'no_adults' => $this->visa->adults,
            'no_children' => $this->visa->childreen,
            'country_name' => $this->visa->country ?? null,
            'travel_date' => $this->visa->travel_date ?? null,
            'appointment' => $this->visa->appointment_date ?? null,
            'visa_notes' => $this->visa->notes ?? null,
        ];
    }
}
