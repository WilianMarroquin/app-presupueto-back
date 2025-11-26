<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class IndexPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            AccountCurrencyPermisosTableSeeder::class,
            AccountPermisosTableSeeder::class,
            AccountTypePermisosTableSeeder::class,
            BudgetPeriodTypePermisosTableSeeder::class,
            BudgetPermisosTableSeeder::class,
            InstallmentPlanPermisosTableSeeder::class,
            TransactionCategoryPermisosTableSeeder::class,
            TransactionPaymentMethodPermisosTableSeeder::class,
            TransactionPermisosTableSeeder::class,
        ]);

    }

}
