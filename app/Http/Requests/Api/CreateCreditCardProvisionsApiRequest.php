<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CreditCardProvisions;


class CreateCreditCardProvisionsApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return CreditCardProvisions::$rules;
    }
}

