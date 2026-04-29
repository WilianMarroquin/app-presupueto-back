<?php

namespace Database\Seeders;

use Database\Seeders\bases\IndexTableSeeder;
use Database\Seeders\permisos\IndexPermisosTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Configuraciones
        $this->call(IndexTableSeeder::class);

        //Catálogos
        $this->call(TransactionCategoryTableSeeder::class);
        $this->call(AccountTypeTableSeeder::class);
        $this->call(AccountCurrencyTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(TransactionPaymentMethodTableSeeder::class);
        $this->call(MenuOpcionesTableSeeder::class);
        $this->call(BudgetPeriodTypeTableSeeder::class);
        $this->call(InstallmentPlanTableSeeder::class);

        //Permisos
        $this->call(IndexPermisosTableSeeder::class);


    }
}
