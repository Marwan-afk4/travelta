<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelTourResource extends JsonResource
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
            'total_price' => $this->total_price,
            'to_name' => $this->to_client->name,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? json_decode($this->to_client->emails)[0] ?? $this->to_client->emails: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? json_decode($this->to_client->phones)[0] ?? $this->to_client->phones: $this->to_client->phone,
            'tour_name' => $this->tour->tour ,
            'tour_type' => $this->tour->type ,
            'children_no' => $this->tour->childreen ,
            'adults_no' => $this->tour->adults ,            'no_adults' => $this->adults->count(),
            'no_children' => $this->children->count(),
            'tour_hotels' => $this->tour->hotel->select('destination', 'hotel_name', 'room_type', 'check_in', 'check_out', 'nights') ,
            'tour_buses' => $this->tour->bus->select('transportation', 'seats') ,
        ];
    }
}
