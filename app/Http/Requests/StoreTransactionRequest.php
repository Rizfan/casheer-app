<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            //
            'total_price' => 'required|integer|min:1000',
            'amount_paid' => 'required|integer|min:0',
            'change_return' => 'required|integer|min:0',
            'status' => 'required|string|in:saved,paid',
            'transaction_details' => 'required|array',
        ];
    }
}
