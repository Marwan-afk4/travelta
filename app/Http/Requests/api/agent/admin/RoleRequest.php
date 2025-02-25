<?php

namespace App\Http\Requests\api\agent\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleRequest extends FormRequest
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
            'name' => ['required'],
            'premisions' => ['array', 'required'],
            'premisions.*.module' => ['required', 'in:supplier,setting_tax,setting_group,setting_currency,request,lead,invoice,inventory_tour,inventory_room,department,customer,bookings,booking_engine,manuel_booking,admin_position,admin,wallet,financial,supplier_payment,revenue,payment_receivable,general_ledger,OE,expenses,booking_payment,HRM_department,HRM_agent,HRM_employee,expenses_category'],
            'premisions.*.action' => ['required', 'in:view,add,update,delete'],
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
