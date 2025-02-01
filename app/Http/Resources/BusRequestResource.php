<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusRequestResource extends JsonResource
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
            
            'notes' => $this->bus->notes ?? null,
            'from' => $this->bus->from ?? null,
            'to' => $this->bus->to ?? null,
            'depature' => $this->bus->departure ?? null,
            'arrival' => $this->bus->arrival ?? null,
            'no_adults' => $this->bus->adults ?? null,
            'no_children' => $this->bus->childreen ?? null,
            'bus_name' => $this->bus->bus ?? null,
            'bus_no' => $this->bus->bus_number ?? null,
            'driver_phone' => $this->bus->driver_phone ?? null, 
        ];
    }
}
