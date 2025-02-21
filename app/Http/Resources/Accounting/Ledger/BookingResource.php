<?php

namespace App\Http\Resources\Accounting\Ledger;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $check_in = null;
        $check_out = null;
        if (!empty($this->manuel_booking->hotel ?? null)) {
            $check_in = $this->manuel_booking->hotel->check_in ?? null;
            $check_out = $this->manuel_booking->hotel->check_out ?? null;
        }
        elseif (!empty($this->manuel_booking->bus ?? null)) {
            $check_in = $this->manuel_booking->bus->departure->format('Y-m-d') ?? null;
            $check_out = $this->manuel_booking->bus->arrival->format('Y-m-d') ?? null;
        }
        elseif (!empty($this->manuel_booking->visa ?? null)) {
            $check_in = $this->manuel_booking->visa->travel_date ?? null; 
        }
        elseif (!empty($this->manuel_booking->flight ?? null)) {
            $check_in = $this->manuel_booking->flight->departure->format('Y-m-d') ?? null;
            $check_out = $this->manuel_booking->flight->arrival->format('Y-m-d') ?? null;
        }
        elseif (!empty($this->manuel_booking->tour ?? null)) {
            $check_in = $this->manuel_booking?->tour?->hotel?->min('check_in') ?? null;
            $check_out = $this->manuel_booking?->tour?->hotel?->max('check_out') ?? null;
        }
        return [
            'date' => $this->date,
            'amount' => $this->amount,
            'currency' => $this->manuel_booking->currency->name ?? null,
            'financial' => $this->financial->name ?? null,
            'type' => 'Manuel Booking',
            'check_in' => $check_in,
            'check_out' => $check_out,
            'booking_code' => $this->manuel_booking->code ?? null,
            'total' => $this->manuel_booking->total_price ?? null, 
            'service' => $this->manuel_booking->service->service_name ?? null,
            'to_name' => $this->manuel_booking->to_client->name ?? null,
            'to_phone' => $this->manuel_booking->to_client->phone ?? $this->manuel_booking->to_client->phones[0] ?? 
            $this->manuel_booking->to_client->phones ?? null,
        ];
    }
}
