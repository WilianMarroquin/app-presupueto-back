<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BudgetItemDetail;

class UpdateBudgetItemDetailApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return BudgetItemDetail::$rules;
    }
}

