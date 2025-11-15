<?php

namespace Database\Seeders;

use App\Models\AccountType;
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

        AccountType::create(['name' => 'Bank']);
        AccountType::create(['name' => 'Cash']);
        AccountType::create(['name' => 'Credit Card']);
        AccountType::create(['name' => 'Wallet']);

        enableForeignKeys();
    }
}
