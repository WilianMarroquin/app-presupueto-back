<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BudgetPeriod;


class CreateBudgetPeriodApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return BudgetPeriod::$rules;
    }
}

