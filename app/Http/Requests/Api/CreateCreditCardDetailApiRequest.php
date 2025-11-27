<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CreditCardDetail;


class CreateCreditCardDetailApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return CreditCardDetail::$rules;
    }
}

