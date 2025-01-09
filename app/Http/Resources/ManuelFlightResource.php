<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelFlightResource extends JsonResource
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
            'flight_type' => $this->flight->type,
            'flight_direction' => $this->flight->direction,
            'depature' => $this->flight->departure,
            'arrival' => $this->flight->arrival,
            'from_to' => $this->flight->from_to,
            'children_no' => $this->flight->childreen,
            'adults_no' => $this->flight->adults,
            'infants_no' => $this->flight->infants,
            'flight_class' => $this->flight->class,
            'airline' => $this->flight->airline,
            'ticket_no' => $this->flight->ticket_number,
            'ref_pnr' => $this->flight->ref_pnr,            
            'no_adults' => $this->adults->count(),
            'no_children' => $this->children->count(),
        ];
    }
}
