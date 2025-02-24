<?php

namespace App\Http\Resources\Accounting\Ledger;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpensesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title ?? null,
            'date' => $this->date ?? null,
            'amount' => $this->amount ?? null,
            'description' => $this->description ?? null,
            'currency' => $this->currency->name ?? null,
            'category' => $this->category->name ?? null,
            'financial' => $this->financial->name ?? null,
            'type' => 'Expenses',
        ];
    }
}
