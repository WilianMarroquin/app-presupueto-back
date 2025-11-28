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
