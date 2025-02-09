<?php

namespace App\Http\Requests\BookinEngine;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookingEngineListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'to_agent_id' => ['nullable', 'exists:agents,id'],
            'to_customer_id' => ['nullable', 'exists:customers,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date'],
            'room_type' => ['required'],
            'no_of_adults' => ['required','integer','min:1'],
            'no_of_children' => ['required','integer','min:0'],
            'payment_status' => ['required','in:later,full,partial,half'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ],400),);
    }
}
