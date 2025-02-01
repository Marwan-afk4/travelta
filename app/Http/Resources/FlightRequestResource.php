<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightRequestResource extends JsonResource
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
            
            'notes' => $this->flight->notes ?? null,
            'flight_type' => $this->flight->type ?? null,
            'flight_direction' => $this->flight->direction ?? null,
            'depature' => $this->flight->departure ?? null,
            'arrival' => $this->flight->arrival ?? null,
            'from_to' => $this->flight->from_to ?? null,
            'children_no' => $this->flight->childreen ?? null,
            'adults_no' => $this->flight->adults ?? null,
            'infants_no' => $this->flight->infants ?? null,
            'flight_class' => $this->flight->class ?? null,
            'airline' => $this->flight->airline ?? null,
            'ticket_no' => $this->flight->ticket_number ?? null,
            'ref_pnr' => $this->flight->ref_pnr ?? null,
        ];
    }
}
