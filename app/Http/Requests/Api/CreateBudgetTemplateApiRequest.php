<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BudgetTemplate;


class CreateBudgetTemplateApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return BudgetTemplate::$rules;
    }
}

