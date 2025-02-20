<?php

namespace App\Http\Resources\Accounting\Ledger;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_code' => $this->code ?? null,
            'date' => $this->date ?? null,
            'currency' => $this->currency->name ?? null,
            'financial' => $this->financial->name ?? null,
            'supplier_name' => $this->supplier->agent ?? null,
            'supplier_phone' => is_string($this->supplier->phones) ? 
            $this->supplier->phones : strval($this->supplier->phones[0]) ?? null,
            'cost' => $this->manuel->cost ?? null,
            'manuel_code' => $this->manuel->code ?? null,
            'type' => 'Payment to Supplier',
        ];
    }
}
