<?php

function disableForeignKeys(): void
{
    if (config('database.default') == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }
}

function enableForeignKeys(): void
{
    if (config('database.default') == 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

function currentAccountingPeriod(): \App\Models\BudgetPeriod
{
    return \App\Models\BudgetPeriod::onlyCurrentAccountingPeriod()
        ->first();
}

function usuarioAutenticado(): \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
{
    return auth()->user() ?? auth('api')->user();
}
