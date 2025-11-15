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

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});
