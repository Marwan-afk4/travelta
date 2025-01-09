<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriperResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $email = null;
        $phone = null;
        if (isset($this->to_client->emails)) {
            $email = json_decode($this->to_client->emails) ?json_decode($this->to_client->emails)[0]: $this->to_client->emails;
            $phone = json_decode($this->to_client->phones) ?json_decode($this->to_client->phones)[0]: $this->to_client->phones;
        }
        else{
            $email = $this->to_client->email;
            $phone = $this->to_client->phone;
        }
        return [
            'supplier_from_name' => $this->from_supplier->agent ?? null,
            'supplier_from_email' => json_decode($this->emails) ?? $this->emails,
            'supplier_from_phone' => json_decode($this->phones) ?? $this->phones,
            'country' => $this->country->name ?? null,
            'total_price' => $this->total_price ?? null,
            'to_name' => $this->to_client->name ?? null,
            'to_role' => isset($this->to_client->agent) ? 'Supplier' : 'Customer',
            'to_email' => $email,
            'to_phone' => $phone,
        ];
    }
}
