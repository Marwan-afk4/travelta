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
        $locale = app()->getLocale(); // Use the application's current locale
        return [
            'id' => $this->id,
            'name' => $this->name ?? $this->f_name . ' ' . $this->l_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'role' => $this->role,
            'plan' => $this->whenLoaded('plan')->name,
            'price' => $this->whenLoaded('plan')->price,
        ];
    }
}
