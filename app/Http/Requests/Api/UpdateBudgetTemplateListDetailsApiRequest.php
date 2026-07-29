<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BudgetTemplateListDetails;

class UpdateBudgetTemplateListDetailsApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return BudgetTemplateListDetails::$rules;
    }
}

