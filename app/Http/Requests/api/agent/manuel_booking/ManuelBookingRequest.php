<?php

namespace App\Http\Requests\api\agent\manuel_booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ManuelBookingRequest extends FormRequest
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
            'to_supplier_id' => ['exists:supplier_agents,id', 'nullable'],
            'to_customer_id' => ['exists:customers,id', 'nullable'],
            'from_supplier_id' => ['required', 'exists:supplier_agents,id'],
            'from_service_id' => ['required', 'exists:services,id'],
            'cost' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'currency_id' => ['required', 'exists:currency_agents,id'],
            'tax_type' => ['required', 'in:include,exclude'],
            'total_price' => ['required', 'numeric'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'mark_up' => ['required', 'numeric'],
            'mark_up_type' => ['required', 'in:value,precentage'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors' => $validator->errors(),
        ], 400));
    }
}
