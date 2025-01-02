<?php

namespace App\Http\Requests\api\auth\agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SginUpAgentRequest extends FormRequest
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
        if ($this->input('role') == 'agent') {
            return [
                'name' => ['required'],
                'phone' => ['required', 'unique:agents'],
                'email' => ['required', 'unique:agents', 'email'],
                'address' => ['required'],
                'password' => ['required'],
                'role' => ['required', 'in:agent,supplier'],
                'country_id' => ['required', 'exists:countries,id'],
                'city_id' => ['required', 'exists:cities,id'],
                'source_id' => ['required', 'exists:customer_sources,id'],
                'owner_name' => ['required'],
                'owner_phone' => ['required', 'unique:agents'],
                'owner_email' => ['required', 'email', 'unique:agents'],
                'tax_card_image' => ['required'],
                'tourism_license_image' => ['required'],
                'commercial_register_image' => ['required'],
            ];
        } 
        else {
            return [
                'name' => ['required'],
                'phone' => ['required', 'unique:agents'],
                'email' => ['required', 'unique:agents', 'email'],
                'address' => ['required'],
                'password' => ['required'],
                'role' => ['required', 'in:agent,supplier'],
                'country_id' => ['required', 'exists:countries,id'],
                'city_id' => ['required', 'exists:cities,id'],
                'source_id' => ['required', 'exists:customer_sources,id'],
                'owner_name' => ['required'],
                'owner_phone' => ['required', 'unique:agents'],
                'owner_email' => ['required', 'email', 'unique:agents'],
                'tax_card_image' => ['required'],
                'tourism_license_image' => ['required'],
                'commercial_register_image' => ['required'],
                'services' => ['required'],
                'services.*' => ['in:hotels,tours,flight,visas,service,umrah,activities']
            ];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors' => $validator->errors(),
        ], 400));
    }
}
