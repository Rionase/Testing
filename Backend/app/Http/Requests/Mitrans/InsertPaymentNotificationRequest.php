<?php

namespace App\Http\Requests\Mitrans;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InsertPaymentNotificationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "order_id" => [ "required", "string", "exists:order,id" ],
            "gross_amount" => [ "required", "numeric" ],
            "payment_type" => [ "required", "string" ],
            "transaction_id" => [ "required", "string" ],
            "transaction_time" => [ "required", "date_format:Y-m-m H:i:s" ],
            "transaction_status" => [ "required", "string" ],
        ];
    }
}
