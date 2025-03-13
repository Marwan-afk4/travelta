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
            'id' => $this->id,
            'supplier_from_name' => $this->from_supplier->agent ?? null,
            'supplier_from_email' => isset($this?->from_supplier?->emails[0]) ? $this->from_supplier->emails[0]
            : $this->from_supplier->emails ?? null,
            'supplier_from_phone' => isset($this?->from_supplier?->phones[0]) ? $this->from_supplier->phones[0]
            : $this->from_supplier->phones ?? null,
            'country' => $this->country->name ?? null,
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_client->name ?? null,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? strval($this->to_client->phones[0]): $this->to_client->phone,
            'from' => $this->bus->from ?? null,
            'to' => $this->bus->to ?? null,
            'depature' => $this->bus->departure ?? null,
            'arrival' => $this->bus->arrival ?? null,
            'no_adults' => $this->bus->adults ?? null,
            'no_children' => $this->bus->childreen ?? null,
            'bus_name' => $this->bus->bus ?? null,
            'bus_no' => $this->bus->bus_number ?? null,
            'driver_phone' => $this->bus->driver_phone ?? null,
            'created_at' => $this->created_at ?? null,
            'code' => $this->code ?? null,
            'payment_status' => $this->payment_type ?? null,
            'status' => $this->status ?? null,
            'special_request' => $this->special_request ?? null,
        ];
    }
}
