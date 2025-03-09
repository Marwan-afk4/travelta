<?php

namespace App\Http\Requests\api\agent\manuel_booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FlightRequest extends FormRequest
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
            'type' => ['required', 'in:domestic,international'],
            'direction' => ['required', 'in:one_way,round_trip,multi_city'],
            'from_to' => ['required'],
            'departure' => ['required', 'date'],
            'arrival' => ['required', 'date'],
            'class' => ['required'],
            'adults' => ['required'],
            'childreen' => ['required'],
            'infants' => ['required'],
            'airline' => ['required'],
            'ticket_number' => ['required'],
            'adult_price' => ['required', 'float'],
            'child_price' => ['required', 'float'],
            'ref_pnr' => ['required'],
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
