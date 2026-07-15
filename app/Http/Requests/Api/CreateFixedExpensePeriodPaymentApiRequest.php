<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FixedExpensePeriodPayment;


class CreateFixedExpensePeriodPaymentApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return FixedExpensePeriodPayment::$rules;
    }
}

