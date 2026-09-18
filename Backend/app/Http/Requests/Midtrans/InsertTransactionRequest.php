<?php

namespace App\Http\Requests\Midtrans;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InsertTransactionRequest extends FormRequest
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
            'params' => [ 'array', 'required' ],
            'params.transaction_details.order_id' => [ 'required', 'integer' ],
            'params.transaction_details.gross_amount' => [ 'required', 'integer' ]
        ];
    }
}
