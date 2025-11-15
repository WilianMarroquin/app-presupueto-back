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
            'name' => 'Cooperativa Guayacán (Transaccional)',
            'type_id' => AccountType::BANK,
            'currency_id' => AccountCurrency::QUETZAL,
            'initial_balance' => 0,
            'current_balance' => 0,
            'is_active' => 1,
        ]);

        Account::create([
            'name' => 'Cooperativa Guayacán (Ahorro)',
            'type_id' => AccountType::BANK,
            'currency_id' => AccountCurrency::QUETZAL,
            'initial_balance' => 0,
            'current_balance' => 0,
            'is_active' => 1,
        ]);

        enableForeignKeys();
    }
}
