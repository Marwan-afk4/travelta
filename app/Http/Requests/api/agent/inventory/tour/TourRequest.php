<?php

namespace App\Http\Requests\api\agent\inventory\tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TourRequest extends FormRequest
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
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // destinations [{, , , }]
        return [
            'name' => ['required'],
            'tour_type' => ['required','in:private,group'],
            'arrival' => ['required', 'date'],
            'status' => ['required','boolean'],
            'days' => ['required','numeric'],
            'nights' => ['required','numeric'],
            'tour_type_id' => ['required','exists:tour_types,id'],
            'featured' => ['required','in:yes,no'],
            'featured_from' => ['required','date'],
            'featured_to' => ['required','date'],
            'deposit' => ['required','numeric'],
            'deposit_type' => ['required','in:precentage,fixed'],
            'tax' => ['required','numeric'],
            'tax_type' => ['required','in:precentage,fixed'],
            'pick_up_country_id' => ['required','exists:countries,id'],
            'pick_up_city_id' => ['required','exists:cities,id'],
            'pick_up_map' => ['required'],
            'destination_type' => ['required','in:single,multiple'],
            'cancelation' => ['required','boolean'],

            'destinations' => ['required', 'array'],
            'destinations.*.country_id' => ['required', 'exists:countries,id'],
            'destinations.*.city_id' => ['required', 'exists:cities,id'],
            'destinations.*.arrival_map' => ['required'],

            'availability.*.date' => ['required','date'],
            'availability.*.last_booking' => ['required','date'],
            'availability.*.quantity' => ['required','numeric'],

            'cancelation_items.*.type' => ['required','in:precentage,fixed'],
            'cancelation_items.*.amount' => ['required','numeric'],
            'cancelation_items.*.days' => ['required','numeric'],

            'excludes.*.name' => ['required'],

            'includes.*.name' => ['required'],

            'itinerary.*.day_name' => ['required'],
            'itinerary.*.content' => ['required'],

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors' => $validator->errors(),
        ],400));
    }
}
