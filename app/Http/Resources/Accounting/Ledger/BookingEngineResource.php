<?php

namespace App\Http\Resources\Accounting\Ledger;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingEngineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->created_at->format('Y-m-d'),
            'amount' => $this->amount,
            'currency' => $this->currency->name ?? null,
            'financial' => 'Wallet',
            'type' => 'Booking Engine',
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'booking_code' => $this->code,
            'total' => $this->amount, 
            'service' => 'Hotel',
            'to_name' => $this->to_client->name ?? null,
            'to_phone' => $this->to_client->phone ?? null,
        ];
    }
}
