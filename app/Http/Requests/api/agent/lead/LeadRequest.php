<?php

namespace App\Http\Requests\api\agent\lead;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LeadRequest extends FormRequest
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
            'name' => ['required'],
            'phone' => ['required'],
            'email' => ['required', 'email'],
            'gender' => ['required', 'in:male,female'],
            'image' => ['sometimes'],
            'watts' => ['sometimes'],
            'source_id' => ['required', 'exists:customer_sources,id'],
            'agent_sales_id' => ['required', 'exists:hrm_employees,id'],
            'service_id' => ['required', 'exists:services,id'],
            'nationality_id' => ['required', 'exists:nationalities,id'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'status' => ['required', 'boolean'],
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
