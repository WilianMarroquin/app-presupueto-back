<?php

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

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});

