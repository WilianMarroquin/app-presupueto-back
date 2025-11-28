<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountCurrency;
use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        Account::truncate();

        Account::create([
            'name' => 'Cooperativa Guayacán',
            'type_id' => AccountType::BANK,
            'currency_id' => AccountCurrency::QUETZAL,
            'initial_balance' => 0,
            'current_balance' => 0,
            'nature' => 'asset',
            'is_active' => 1,
            'description' => 'Cuenta de Ahorros en Cooperativa Guayacán',
        ]);

        Account::create([
            'name' => 'Cooperativa Guayacán (Ahorro)',
            'type_id' => AccountType::BANK,
            'currency_id' => AccountCurrency::QUETZAL,
            'initial_balance' => 0,
            'current_balance' => 0,
            'nature' => 'asset',
            'is_active' => 1,
            'description' => 'Cuenta Transaccional',
        ]);

//        Account::create([
//            'name' => 'TC BAC Credomatic',
//            'type_id' => AccountType::CREDIT_CARD,
//            'currency_id' => AccountCurrency::QUETZAL,
//            'initial_balance' => 0,
//            'current_balance' => 0,
//            'nature' => 'liability',
//            'is_active' => 1,
//            'description' => 'Tarjeta de Crédito BAC Credomatic',
//        ]);
//
//        Account::create([
//            'name' => 'TC Cooperativa Guayacán',
//            'type_id' => AccountType::CREDIT_CARD,
//            'currency_id' => AccountCurrency::QUETZAL,
//            'initial_balance' => 0,
//            'current_balance' => 0,
//            'nature' => 'liability',
//            'is_active' => 1,
//            'description' => 'Tarjeta de Crédito Cooperativa Guayacán',
//        ]);

        enableForeignKeys();
    }
}
