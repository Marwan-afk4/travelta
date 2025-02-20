<?php

namespace App\Http\Requests\api\agent\accounting\expenses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpensesRequest extends FormRequest
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
            'category_id' => ['required', 'exists:expenses_categories,id'],
            'financial_id' => ['required', 'exists:finantiol_acountings,id'],
            'currency_id' => ['required', 'exists:currency_agents,id'],
            'title' => ['required'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'description' => ['sometimes'],
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
