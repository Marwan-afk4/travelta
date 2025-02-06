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
            'id' => $this->id,
            'supplier_from_name' => $this->from_supplier->agent ?? null,
            'supplier_from_email' => is_string($this->from_supplier->emails) ? 
            json_decode($this->from_supplier->emails)[0] ?? $this->from_supplier->emails 
            : $this->from_supplier->emails,
            'supplier_from_phone' => is_string($this->from_supplier->emails) ? 
            json_decode($this->from_supplier->phones)[0] ?? $this->from_supplier->phones
            : $this->from_supplier->phones,
            'country' => $this->country->name ?? null, 
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_client->name ?? null,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? $this->to_client->phones[0]: $this->to_client->phone,
            'no_adults' => $this->adults->count(),
            'no_children' => $this->children->count(),
            'country_name' => $this->visa->country ?? null,
            'travel_date' => $this->visa->travel_date ?? null,
            'appointment' => $this->visa->appointment_date ?? null,
            'notes' => $this->visa->notes ?? null,
            'created_at' => $this->created_at ?? null,
            'code' => $this->code ?? null,
            'payment_status' => $this->payment_type ?? null,
            'status' => $this->status ?? null,
        ];
    }
}
