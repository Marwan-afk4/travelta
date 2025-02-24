<?php

namespace App\Http\Resources\Accounting\Ledger;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->created_at->format('Y-m-d') ?? null,
            'amount' => $this->amount ?? null,
            'currency' => $this->currency->name ?? null,
            'financial' => $this->financial->name ?? null,
            'owner_name' => $this->owner->name ?? null,
            'owner_phone' => $this->owner->phone ?? null,
            'transaction_type' => $this->type ?? null,
            'type' => 'Owner',
        ];
    }
}
