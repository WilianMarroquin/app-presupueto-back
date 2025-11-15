<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TransactionPaymentMethod;

class UpdateTransactionPaymentMethodApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return TransactionPaymentMethod::$rules;
    }
}

