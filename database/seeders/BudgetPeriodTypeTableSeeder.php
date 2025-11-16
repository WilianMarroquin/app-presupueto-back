<?php

namespace Database\Seeders;

use App\Models\BudgetPeriodType;
use Illuminate\Database\Seeder;

class BudgetPeriodTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        BudgetPeriodType::truncate();

        BudgetPeriodType::create([
            'name' => 'Mensual',
            'description' => 'Periodo de presupuesto mensual'
        ]);
        BudgetPeriodType::create([
            'name' => 'Trimestral',
            'description' => 'Periodo de presupuesto trimestral'
        ]);
        BudgetPeriodType::create([
            'name' => 'Anual',
            'description' => 'Periodo de presupuesto anual'
        ]);

        enableForeignKeys();
    }
}
