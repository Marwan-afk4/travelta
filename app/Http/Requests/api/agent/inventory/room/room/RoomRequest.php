<?php

namespace App\Http\Requests\api\agent\inventory\room\room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoomRequest extends FormRequest
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
            'status' => ['required', 'boolean'],
            'price_type' => ['required', 'in:fixed,variable'],
            'price' => ['numeric'],
            'quantity' => ['required', 'numeric'],
            'max_adults' => ['required', 'numeric'],
            'max_children' => ['required', 'numeric'],
            'max_capacity' => ['required', 'numeric'],
            'min_stay' => ['required', 'numeric'],
            'room_type_id' => ['required', 'exists:room_types,id'],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'hotel_meal_id' => ['exists:hotel_meals,id'],
            'currency_id' => ['exists:currency_agents,id'],
            'b2c_markup' => ['required', 'numeric'],
            'b2e_markup' => ['required', 'numeric'],
            'b2b_markup' => ['required', 'numeric'],
            'tax_type' => ['required', 'in:include,exclude,include_except'],
            'check_in' => ['required'],
            'check_out' => ['required'], 
            'cancelation' => ['required', 'in:free,non_refunable'],
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
