<?php

namespace App\Http\Requests\BookinEngine;

use Illuminate\Foundation\Http\FormRequest;

class BookingEngineListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_supplier_id' => ['required', 'exists:agents,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'to_agent_id' => ['required', 'exists:agents,id'],
            'to_customer_id' => ['required', 'exists:customers,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date'],
            'room_type' => ['required'],
            
        ];
    }
}
