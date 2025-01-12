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
        return [
            'id' => $this->id,
            'name' => $this->f_name . ' ' . $this->l_name ?? null,
            'email' => $this->email,
            'phone' => $this->phone,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'role' => $this->role,
            'plan' => $this->plan->name ?? null,
            'price' => $this->plan->price ?? null,
        ];
    }
}
