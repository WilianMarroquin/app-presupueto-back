<?php

namespace Database\Seeders;

use App\Models\AccountCurrency;
use Illuminate\Database\Seeder;

class AccountCurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        AccountCurrency::truncate();
        AccountCurrency::create([
            'name' => 'Quetzal',
            'code' => 'GTQ',
            'symbol' => 'Q',
        ]);
        AccountCurrency::create([
            'name' => 'Dollar USA',
            'code' => 'USD',
            'symbol' => '$',
        ]);
        enableForeignKeys();
    }
}
