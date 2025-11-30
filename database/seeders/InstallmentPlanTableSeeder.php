<?php

namespace Database\Seeders;

use App\Models\InstallmentPlan;
use Illuminate\Database\Seeder;

class InstallmentPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        InstallmentPlan::truncate();

        InstallmentPlan::create([
            'name' => 'Apple Watch',
            'total_amount' => 1799,
            'total_installments' => 12,
            'interest_rate' => 0,
            'start_date' => '2024-12-29',
            'account_id' => 4,
            'status' => 'active',
        ]);

        InstallmentPlan::create([
            'name' => 'Monito, Teclado y Mouse',
            'total_amount' => 2467.56,
            'total_installments' => 12,
            'interest_rate' => 0,
            'start_date' => '2025-27-04',
            'account_id' => 4,
            'status' => 'active',
        ]);

        InstallmentPlan::create([
            'name' => 'Iphone 12 Pro',
            'total_amount' => 2650,
            'total_installments' => 12,
            'interest_rate' => 0,
            'start_date' => '2025-08-31',
            'account_id' => 4,
            'status' => 'active',
        ]);

        InstallmentPlan::create([
            'name' => 'Closet',
            'total_amount' => 2500,
            'total_installments' => 10,
            'interest_rate' => 0,
            'start_date' => '2025-11-23',
            'account_id' => 4,
            'status' => 'active',
        ]);

        enableForeignKeys();
    }
}
