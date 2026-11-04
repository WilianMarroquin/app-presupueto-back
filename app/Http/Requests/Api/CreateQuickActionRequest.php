<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuickActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:transaction_categories,id'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'payment_method_id' => ['nullable', 'integer', 'exists:transaction_payment_methods,id'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:30'],
        ];
    }
}
