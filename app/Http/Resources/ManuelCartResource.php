<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\SupplierAgent;

class ManuelCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'supplier_from_name' => SupplierAgent::where('id', $this->from_supplier_id->id)->first()->name ?? null,
   
        ];
    }
}
