<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InsertOrderRequest extends FormRequest
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
            'customer_name' => [ 'required', 'string', 'max:256' ],
            'customer_email' => [ 'required', 'string', 'email', 'max:256' ],
            'customer_phone' => [ 'required', 'string', 'max:20' ],
            'notes' => [ 'nullable', 'string', 'max:256' ],
            'list_product' => [ 'required', 'array', 'min:1' ],
            'list_product.*.id_product' => [ 'required', 'integer', 'distinct', 'exists:product,id' ],
            'list_product.*.quantity' => [ 'required', 'integer', 'min:1' ],
        ];
    }
}
