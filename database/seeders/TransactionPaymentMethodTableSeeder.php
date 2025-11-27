<?php

namespace Database\Seeders;

use App\Models\TransactionPaymentMethod;
use Illuminate\Database\Seeder;

class TransactionPaymentMethodTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        TransactionPaymentMethod::truncate();

        TransactionPaymentMethod::create(['name' => 'Efectivo']);
        TransactionPaymentMethod::create(['name' => 'Tarjeta de débito']);
        TransactionPaymentMethod::create(['name' => 'Transferencia']);
        TransactionPaymentMethod::create(['name' => 'Tarjeta de Crédito']);

        enableForeignKeys();

    }
}
