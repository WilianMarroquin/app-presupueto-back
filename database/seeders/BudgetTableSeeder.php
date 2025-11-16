<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\BudgetPeriodType;
use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class BudgetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        Budget::truncate();

        Budget::create([
            'amount' => 800,
            'category_id' => TransactionCategory::VIVIENDA,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 1000,
            'category_id' => TransactionCategory::ALIMENTACION,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 325,
            'category_id' => TransactionCategory::TRANSPORTE,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 250,
            'category_id' => TransactionCategory::SALUD,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 250,
            'category_id' => TransactionCategory::EDUCACION,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 500,
            'category_id' => TransactionCategory::ENTRETENIMIENTO,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 300,
            'category_id' => TransactionCategory::CUIDADO_PERSONAL,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 0,
            'category_id' => TransactionCategory::SEGUROS,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 3100,
            'category_id' => TransactionCategory::DEUDAS_Y_PRESTAMOS,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        Budget::create([
            'amount' => 300,
            'category_id' => TransactionCategory::OTROS_GASTOS,
            'period_types_id' => BudgetPeriodType::MENSUAL,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        enableForeignKeys();
    }
}


