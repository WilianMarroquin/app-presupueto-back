<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\TransactionPaymentMethod;
use Illuminate\Database\Seeder;

class AccountTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        disableForeignKeys();

        AccountType::truncate();

        $bank       = AccountType::create(['name' => 'Bank']);
        $cash       = AccountType::create(['name' => 'Cash']);
        $creditCard = AccountType::create(['name' => 'Credit Card']);
//        $wallet     = AccountType::create(['name' => 'Wallet']);

        $bank->movementMethods()->sync([
            TransactionPaymentMethod::TRANSFERENCIA,
            TransactionPaymentMethod::TARJETA_DE_DEBITO,
        ]);

        $cash->movementMethods()->sync([
            TransactionPaymentMethod::EFECTIVO,
        ]);

        $creditCard->movementMethods()->sync([
            TransactionPaymentMethod::TARJETA_DE_CREDITO,
        ]);

//        $wallet->movementMethods()->sync([
//            TransactionPaymentMethod::TRANSFERENCIA,
//            TransactionPaymentMethod::EFECTIVO,
//        ]);

        enableForeignKeys();
    }
}
