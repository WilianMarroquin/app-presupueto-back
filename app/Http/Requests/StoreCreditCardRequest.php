<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Account;
use Illuminate\Validation\Rule;

class StoreCreditCardRequest extends FormRequest
{
    public function authorize()
    {
        return true; // O tu lógica de permisos
    }

    public function rules()
    {
        // Reglas específicas de la tarjeta
        $cardRules = [
            'bank_name' => ['required', 'string', 'max:100'],
            'alias' => ['required', 'string', 'max:50'], // Alias corto es mejor
            // Tip: Usa Rule::in para que sea más legible
            'network' => ['required', Rule::in(['Visa', 'MasterCard', 'American Express', 'Discover'])],
            'color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'last_4' => ['required', 'digits:4'], // 'digits:4' es más preciso que regex
            'cutoff_day' => ['required', 'integer', 'between:1,31'],
            'payment_day' => ['required', 'integer', 'between:1,31'],
            'currency_id' => ['required', 'exists:currencies,id'], // Validar que la moneda exista
        ];

        // Fusión con reglas de Account (si realmente lo necesitas)
        // Personalmente, prefiero definirlas aquí explícitamente para evitar efectos secundarios
        // si cambias las reglas generales de Account.
        return array_merge($cardRules, Account::$rules);
    }

    public function messages()
    {
        return [
            'last_4.digits' => 'Solo necesitamos los últimos 4 dígitos.',
            'color.regex' => 'El color debe ser un Hex válido (ej: #FFFFFF).',
        ];
    }
}
