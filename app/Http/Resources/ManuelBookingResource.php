<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManuelBookingResource extends JsonResource
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
            'supplier_from_email' => isset($this?->from_supplier?->emails[0]) ? $this->from_supplier->emails[0]
            : $this->from_supplier->emails ?? null,
            'supplier_from_phone' => isset($this?->from_supplier?->phones[0]) ? $this->from_supplier->phones[0]
            : $this->from_supplier->phones ?? null,
            'country' => $this->country->name ?? null,
            'total_price' => number_format($this->total_price, 2, '.', ''),
            'to_name' => $this->to_client->name ?? null,
            'to_role' => $this->to_client->agent ? 'Supplier' : 'Customer',
            'to_email' => $this->to_client->emails ? $this->to_client->emails[0]: $this->to_client->email,
            'to_phone' => $this->to_client->phones ? $this->to_client->phones[0]: $this->to_client->phone,
            'hotel' => $this->hotel,
            'bus' => $this->bus,
            'flight' => $this->flight,
            'tour' => $this->tour,
            'visa' => $this->visa,
            'voucher' => $this->voucher_link ?? null,
            'currency' => $this?->currency?->name ?? null,
        ];
    }
}
