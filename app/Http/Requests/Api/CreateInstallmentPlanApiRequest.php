<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\InstallmentPlan;


class CreateInstallmentPlanApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return InstallmentPlan::$rules;
    }
}

