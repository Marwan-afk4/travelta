<?php

namespace App\Http\Resources\Request;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
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
            'revenue' => (float)$this->expected_revenue,
            'service' => 'Hotel',
            'stages' => $this->stages,
            'currecy' => $this->currency->name,
            'to_name' => $this->customer?->name ?? null,
            'to_phone' => $this->customer?->phone ?? null,
            'agent' => $this->admin_agent?->name,
            'from' => $this->hotel->check_in ?? null,
            'to' => $this->hotel->check_out ?? null,
        ];
    }
}
