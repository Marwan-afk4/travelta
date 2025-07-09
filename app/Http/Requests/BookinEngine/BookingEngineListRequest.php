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
            'room_id' => ['required', 'exists:rooms,id'],
            // 'from_supplier_id' => ['nullable', 'exists:agents,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'hotel_id' => ['required', 'exists:hotels,id'],
            // 'to_agent_id' => ['nullable', 'exists:agents,id'],
            'to_customer_id' => ['required', 'exists:customers,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date'],
            'room_type' => ['required'],
            'no_of_adults' => ['required','integer','min:1'],
            'no_of_children' => ['required','integer','min:0'],
            'no_of_nights'=> ['required','integer','min:1'],
            'payment_status' => ['nullable','in:later,full,partial,half'],
            'status' => ['nullable','in:inprogress,done,faild'],
            'currency_id' => ['required', 'exists:currency_agents,id'],
            'special_request' => ['nullable'],
            'amount'=>['required','numeric'],
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
