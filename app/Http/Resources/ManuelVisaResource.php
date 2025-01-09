<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelVisaResource extends JsonResource
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
            'to_name' => $this->to_client->name,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? $this->to_client->phones[0]: $this->to_client->phone,
            'no_adults' => $this->adults->count(),
            'no_children' => $this->children->count(),
            'country_name' => $this->visa->country,
            'travel_date' => $this->visa->travel_date,
            'appointment' => $this->visa->appointment_date,
            'notes' => $this->visa->notes,
        ];
    }
}
