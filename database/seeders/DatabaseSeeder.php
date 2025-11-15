<?php

namespace Database\Seeders;

use Database\Seeders\bases\IndexTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(IndexTableSeeder::class);
        $this->call(TransactionCategoryTableSeeder::class);
        $this->call(AccountTypeTableSeeder::class);
        $this->call(AccountCurrencyTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(TransactionPaymentMethodTableSeeder::class);
        $this->call(MenuOpcionesTableSeeder::class);

    }
}
