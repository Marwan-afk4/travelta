<?php

namespace App\Http\Requests\api\auth\agent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SginUpAffilateRequest extends FormRequest
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
        if ($this->input('image_type') == 'passport') {
            return [
                'f_name' => ['required'],
                'l_name' => ['required'],
                'email' => ['required', 'email', 'unique:affilate_agents'],
                'phone' => ['required'],
                'password' => ['required'],
                'image_type' => ['required', 'in:passport,national'],
                'passport_image' => ['required'],
                'role' => ['required', 'in:affilate,freelancer'],
            ];
        } 
        else {
            return [
                'f_name' => ['required'],
                'l_name' => ['required'],
                'email' => ['required', 'email', 'unique:affilate_agents'],
                'phone' => ['required'],
                'password' => ['required'],
                'image_type' => ['required', 'in:passport,national'],
                'national_image1' => ['required'],
                'national_image2' => ['required'],
                'role' => ['required', 'in:affilate,freelancer'],
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
