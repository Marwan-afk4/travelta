<?php

namespace App\Http\Resources\Accounting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentReceivableResource extends JsonResource
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
            'manuel_code' => $this->code,
            'created' => $this->created_at->format('Y-m-d'),
            'type' => empty($this->to_supplier_id) ? 'Customer': 'Supplier',
            'client_name' => $this->to_client->name,
            'client_phone' => empty($this->to_supplier_id) ? $this->to_client->phone : $this->to_client->phones[0] ?? $this->to_client->phones,
            'total' => $this->total_price,
            'paid' => $this->payments->sum('amount'),
            'remaining' => $this->total_price - $this->payments->sum('amount'),
            'status' => $this->payment_type,
            
        ];
    }
}
