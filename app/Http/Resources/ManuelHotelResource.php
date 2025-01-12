<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelHotelResource extends JsonResource
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
            'hotel_name' => $this->hotel->hotel_name,
            'check_in' => $this->hotel->check_in,
            'check_out' => $this->hotel->check_out,
            'no_nights' => $this->hotel->nights,
            'room_type' => $this->hotel->room_type,
            'no_adults' => $this->hotel->adults,
            'no_childs' => $this->hotel->childreen,            
        ];
    }
}
