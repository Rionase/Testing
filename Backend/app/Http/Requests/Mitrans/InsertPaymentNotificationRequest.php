<?php

namespace App\Http\Requests\Mitrans;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

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
            "transaction_time" => [ "required", "date_format:Y-m-d H:i:s" ],
            "transaction_status" => [ "required", "string" ],
        ];
    }

    /**
     * Apabila request tidak ada header Accept: json/appliction, maka meskipun error pada validation rules, akan meredirect ke page utama ( http://127.0.0.1:8000/ ).
     * dengan adanya failedValidation, maka meskipun tidak ada Accept: json/appliction, maka tetap akan return validation rules json error
     * * @param Validator $validator
     * @throws Throwable
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(message: $validator->errors()->toJson());
    }
}
