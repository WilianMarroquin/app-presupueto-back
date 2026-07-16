<?php

namespace Database\Seeders;

use App\Models\BudgetPeriodType;
use App\Models\BudgetTemplate;
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
        $this->call(TransactionPaymentMethodTableSeeder::class);
        $this->call(AccountTypeTableSeeder::class);
        $this->call(AccountCurrencyTableSeeder::class);
        $this->call(MenuOpcionesTableSeeder::class);
        $this->call(BudgetPeriodTypeTableSeeder::class);

        //Permisos
        $this->call(IndexPermisosTableSeeder::class);


        $plantillaDePrusupuestoPrueba = BudgetTemplate::create([
            'user_id' => 1,
            'name' => 'Test Budget',
            'description' => 'Test Budget',
            'budget_period_type_id' => BudgetPeriodType::MENSUAL,
            'total_estimated_amount' => 0
        ]);

        $plantillaDePrusupuestoPrueba->periods()->create([
            'user_id' => 1,
            'budget_template_id' => 'Test Budget',
            'start_date' => now(),
            'end_date' => null,
            'is_active' => true,
            'total_budgeted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    }
}
