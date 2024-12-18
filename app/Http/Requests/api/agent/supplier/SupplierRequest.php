<?php

namespace App\Http\Requests\api\agent\supplier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierRequest extends FormRequest
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
            'agent' => ['required'],
            'admin_name' => ['required'],
            'admin_phone' => ['required'],
            'admin_email' => ['required', 'email'],
            'emails' => ['required'],
            'phones' => ['required'],
            'role' => ['required', 'in:affilate,freelancer,agent,supplier'],
            'agent_id' => ['required', 'numeric'],
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
