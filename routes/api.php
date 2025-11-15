<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});











Route::apiResource('transaction_categories', App\Http\Controllers\Api\TransactionCategoryApiController::class)
        ->parameters(['transaction_categories' => 'transactioncategory']);

Route::apiResource('account_types', App\Http\Controllers\Api\AccountTypeApiController::class)
        ->parameters(['account_types' => 'accounttype']);
