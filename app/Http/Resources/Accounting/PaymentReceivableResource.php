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
        $due_amount = $this->payments_cart
        ->where('date', '<=', date('Y-m-d'))
        ->sum('due_payment');
        $next_due = $this->payments_cart
        ->where('date', '>', date('Y-m-d'))
        ->where('due_payment', '>', 0)
        ->sortBy('date')
        ->first();
        $first_due = $this->payments_cart
        ->where('date', '<=', date('Y-m-d'))
        ->where('due_payment', '>', 0)
        ->sortBy('date')
        ->first();
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
            'over_due' => $due_amount,
            'next_due' => $next_due->due_payment ?? 0,
            'due_date' => $due_amount <= 0 ? 
            $next_due->date ?? null
            : $first_due->date ?? null,
        ];
    }
}
