<?php

namespace App\Http\Requests\api\agent\manuel_booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartEditBookingRequest extends FormRequest
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
        if ($this->input('payment_type') == 'full') {
            return [
                'total_cart' => ['required', 'numeric'],
                'payment_type' => ['required', 'in:full,partial,later'],
            ];
        }
        elseif ($this->input('payment_type') == 'partial' ) {
            return [
                'total_cart' => ['required', 'numeric'],
                'payment_type' => ['required', 'in:full,partial,later'],
                'payment_methods' => ['required'],
                'payments' => ['required'],
            ];
        }
        elseif ($this->input('payment_type') == 'later') {
            return [
                'total_cart' => ['required', 'numeric'],
                'payment_type' => ['required', 'in:full,partial,later'],
                'payments' => ['required'],
            ];
        }
        else{
            return [ 
                'payment_type' => ['in:full,partial,later'], 
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
