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
            'id' => $this->id,
            'supplier_from_name' => $this->from_supplier->agent ?? null,
            'supplier_from_email' => $this->from_supplier->emails[0] ?? $this->from_supplier->emails,
            'supplier_from_phone' => $this->from_supplier->phones[0] ?? $this->from_supplier->phones,
            'country' => $this->country->name ?? null,
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
            'children_no' => intval($this->flight->childreen) ?? null,
            'adults_no' => intval($this->flight->adults) ?? null,
            'infants_no' => intval($this->flight->infants) ?? null,
            'flight_class' => $this->flight->class ?? null,
            'airline' => $this->flight->airline ?? null,
            'ticket_no' => $this->flight->ticket_number ?? null,
            'ref_pnr' => $this->flight->ref_pnr ?? null,
            'created_at' => $this->created_at ?? null,
            'code' => $this->code ?? null,
            'payment_status' => $this->payment_type ?? null,
            'status' => $this->status ?? null,
            'special_request' => $this->special_request ?? null,
        ];
    }
}
