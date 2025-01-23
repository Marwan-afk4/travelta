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
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_client->name ?? null,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? $this->to_client->phones[0]: $this->to_client->phone,
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
