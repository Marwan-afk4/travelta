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
            'price' => ['nullable', 'numeric'],
            'currency_id' => ['nullable', 'exists:currency_agents,id'],
            'payments_options' => ['required'],
// ________________________________________________________________
            'enable_person_type' => ['required','boolean'],
            'with_accomodation' => ['required','boolean'],
            'enabled_extra_price' => ['required','boolean'],
            
            'discounts.*.from' => ['required', 'numeric'],
            'discounts.*.to' => ['required', 'numeric'],
            'discounts.*.discount' => ['required', 'numeric'],
            'discounts.*.type' => ['required', 'in:precentage,fixed'],

            'extra.*.name' => ['required'],
            'extra.*.price' => ['required', 'numeric'],
            'extra.*.currency_id' => ['required', 'exists:currency_agents,id'],
            'extra.*.type' => ['required', 'in:one_time,person,night'],

            'hotels.*.name' => ['required'],

            'pricing.*.person_type' => ['required', 'in:adult,child,infant'],
            'pricing.*.min_age' => ['nullable', 'numeric'],
            'pricing.*.max_age' => ['nullable', 'numeric'],
            'pricing.*.pricing_item' => ['required'],
            'pricing.*.pricing_item.*.currency_id' => ['required', 'exists:currency_agents,id'],
            'pricing.*.pricing_item.*.price' => ['required', 'numeric'],
            'pricing.*.pricing_item.*.type' => ['required'],

            'tour_room.*.adult_single' => ['required', 'numeric'],
            'tour_room.*.adult_double' => ['required', 'numeric'],
            'tour_room.*.adult_triple' => ['required', 'numeric'],
            'tour_room.*.adult_quadruple' => ['required', 'numeric'],
            'tour_room.*.children_single' => ['required', 'numeric'],
            'tour_room.*.children_double' => ['required', 'numeric'],
            'tour_room.*.children_triple' => ['required', 'numeric'],
            'tour_room.*.children_quadruple' => ['required', 'numeric'],

            'policy' => ['required'],
// ________________________________________________________________
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
