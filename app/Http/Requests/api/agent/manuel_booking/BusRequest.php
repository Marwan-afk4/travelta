<?php

namespace App\Http\Requests\api\agent\manuel_booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusRequest extends FormRequest
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
            'from' => ['required'],
            'to' => ['required'],
            'departure' => ['required', 'date'],
            'arrival' => ['required', 'date'],
            'adults' => ['required', 'numeric'],
            'childreen' => ['required', 'numeric'],
            'adult_price' => ['required', 'numeric'],
            'bus' => ['required'],
            'bus_number' => ['required'],
            'driver_phone' => ['required'],
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
