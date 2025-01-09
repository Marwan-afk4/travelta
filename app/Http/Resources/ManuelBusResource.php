<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelBusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'supplier_from_name' => $this->from_supplier->agent ?? null,
            'supplier_from_email' => json_decode($this->emails) ?? $this->emails,
            'supplier_from_phone' => json_decode($this->phones) ?? $this->phones,
            'country' => $this->country->name,
            'total_price' => (float)$this->total_price,
            'to_name' => $this->to_client->name,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? $this->to_client->phones[0]: $this->to_client->phone,
            'from' => $this->bus->from,
            'to' => $this->bus->to,
            'depature' => $this->bus->departure,
            'arrival' => $this->bus->arrival,
            'no_adults' => $this->bus->adults,
            'no_children' => $this->bus->childreen,
            'bus_name' => $this->bus->bus,
            'bus_no' => $this->bus->bus_number,
            'driver_phone' => $this->bus->driver_phone,            
            'no_adults' => $this->adults->count(),
            'no_children' => $this->children->count(),
        ];
    }
}
