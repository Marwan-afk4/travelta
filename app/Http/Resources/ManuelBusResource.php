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
            'supplier_from_name' => $this->id,
            'supplier_from_email' => $this->name ?? $this->f_name . ' ' . $this->l_name,
            'supplier_from_phone' => $this->email,
            'country' => $this->phone,
            'total_price' => $this->start_date,
            'to_name' => $this->end_date,
            'to_role' => $this->role,
            'to_email' => $this->whenLoaded('plan')->name ?? null,
            'to_phone' => $this->whenLoaded('plan')->price ?? null,
        ];
    }
}
