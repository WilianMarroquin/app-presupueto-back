<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';

    Route::apiResource('transaction_categories', App\Http\Controllers\Api\TransactionCategoryApiController::class)
        ->parameters(['transaction_categories' => 'transactioncategory']);

    Route::apiResource('account_types', App\Http\Controllers\Api\AccountTypeApiController::class)
        ->parameters(['account_types' => 'accounttype']);

    Route::apiResource('account_currencys', App\Http\Controllers\Api\AccountCurrencyApiController::class)
        ->parameters(['account_currencys' => 'accountcurrency']);

    Route::apiResource('accounts', App\Http\Controllers\Api\AccountApiController::class)
        ->parameters(['accounts' => 'account']);

    Route::apiResource('transaction_payment_methods', App\Http\Controllers\Api\TransactionPaymentMethodApiController::class)
        ->parameters(['transaction_payment_methods' => 'transactionpaymentmethod']);

    Route::post('transactions_registrar_ingreso_salario', [App\Http\Controllers\Api\TransactionApiController::class, 'registrarIngresoSalario']);
    Route::post('transactions_registrar_visa_cuotas', [App\Http\Controllers\Api\TransactionApiController::class, 'registrarVisaCuotas']);

    Route::apiResource('transactions', App\Http\Controllers\Api\TransactionApiController::class)
        ->parameters(['transactions' => 'transaction']);

    Route::apiResource('budget_period_types', App\Http\Controllers\Api\BudgetPeriodTypeApiController::class)
        ->parameters(['budget_period_types' => 'budgetperiodtype']);

    Route::apiResource('budgets', App\Http\Controllers\Api\BudgetApiController::class)
        ->parameters(['budgets' => 'budget']);

    Route::apiResource('installment_plans', App\Http\Controllers\Api\InstallmentPlanApiController::class)
        ->parameters(['installment_plans' => 'installmentPlan']);

    Route::apiResource('credit_card_provisions', App\Http\Controllers\Api\CreditCardProvisionsApiController::class)
        ->parameters(['credit_card_provisions' => 'creditcardprovisions']);

    Route::get('analytics/expense/by/categories', [App\Http\Controllers\Api\AnalyticsApiController::class, 'getTotalExpensesByCategory']);

    Route::post('installment_plans/pay', [App\Http\Controllers\Api\InstallmentPlanApiController::class, 'payFee']);

    Route::post('create/credit/card', [App\Http\Controllers\Api\CreditCardApiController::class, 'store']);

    Route::post('credit/card/payment', [App\Http\Controllers\Api\CreditCardApiController::class, 'payment']);

    Route::get('home/details/daily', [App\Http\Controllers\Api\HomeApiController::class, 'detailDaily']);

    Route::apiResource('budget_item_details', App\Http\Controllers\Api\BudgetItemDetailApiController::class)
        ->parameters(['budget_item_details' => 'budgetitemdetail']);

    Route::apiResource('budget_items', App\Http\Controllers\Api\BudgetItemApiController::class)
        ->parameters(['budget_items' => 'budgetitem']);

    Route::post('budget_templates/activar/{budget_template}', [App\Http\Controllers\Api\BudgetTemplateApiController::class, 'activated']);

    Route::apiResource('budget_templates', App\Http\Controllers\Api\BudgetTemplateApiController::class)
        ->parameters(['budget_templates' => 'budgettemplate']);

    Route::apiResource('budget_periods', App\Http\Controllers\Api\BudgetPeriodApiController::class)
        ->parameters(['budget_periods' => 'budgetperiod']);

});

Route::post('/voice-command', [\App\Http\Controllers\Api\VoiceCommandApiController::class, 'store']);

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});


Route::get('/generar-llave-maestra', function () {
    // Busca tu usuario (ID 1 o el que sea)
    $user = User::first();

    // Crea el token
    $token = $user->createToken('iPhone-Jarvis')->plainTextToken;

    return response()->json([
        'token' => $token,
        'mensaje' => 'Copia esto y bórra esta ruta inmediatamente.'
    ]);
});
