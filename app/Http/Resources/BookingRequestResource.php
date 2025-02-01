<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRequestResource extends JsonResource
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
            'to_name' => $this->customer?->name ?? null, 
            'to_phone' => $this->customer?->phone ?? null,
            'agent' => $this->admin_agent?->name ?? null,
            'currecy' => $this->currency->name ?? null,
            'service' => $this->service->service_name ?? null,
            'adults' => $this->adults ?? null,
            'children' => $this->children ?? null,
            'hotel' => $this->hotel ?? null,
            'bus' => $this->bus ?? null,
            'flight' => $this->flight ?? null,
            'visa' => $this->visa ?? null,
            'tour' => $this->expected_tour ?? null,

            'expected_revenue' => $this->expected_revenue ?? null,
            'priority' => $this->priority ?? null,
            'stages' => $this->stages ?? null,
        ];
    }
}
