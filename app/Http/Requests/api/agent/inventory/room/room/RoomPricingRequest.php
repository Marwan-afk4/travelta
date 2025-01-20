<?php

namespace App\Http\Requests\api\agent\inventory\room\room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoomPricingRequest extends FormRequest
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
            'pricing_data_id' => ['required', 'exists:room_pricing_data,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'currency_id' => ['required', 'exists:currency_agents,id'],
            'name' => ['required'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date'],
            'price' => ['required', 'numeric'],
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
