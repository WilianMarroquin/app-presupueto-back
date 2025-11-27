<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        disableForeignKeys();
        TransactionCategory::truncate();
        $categories = [
            // INGRESOS (type = 'income')
            ['name' => 'Salario', 'icon' => 'ri-briefcase-4-fill', 'color' => '#4CAF50', 'type' => 'income'],
            ['name' => 'Negocios', 'icon' => 'ri-rocket-2-fill', 'color' => '#2196F3', 'type' => 'income'],
            ['name' => 'Inversiones', 'icon' => 'ri-line-chart-fill', 'color' => '#9C27B0', 'type' => 'income'],
            ['name' => 'Otros Ingresos', 'icon' => 'ri-gift-2-fill', 'color' => '#FFC107', 'type' => 'income'],

            // GASTOS (type = 'expense')
            ['name' => 'Vivienda', 'icon' => 'ri-home-4-fill', 'color' => '#009688', 'type' => 'expense'],
            ['name' => 'Alimentación', 'icon' => 'ri-restaurant-2-fill', 'color' => '#FF5722', 'type' => 'expense'],
            ['name' => 'Transporte', 'icon' => 'ri-car-fill', 'color' => '#F44336', 'type' => 'expense'],
            ['name' => 'Salud & Bienestar', 'icon' => 'ri-heart-pulse-fill', 'color' => '#E91E63', 'type' => 'expense'],
            ['name' => 'Ocio & Social', 'icon' => 'ri-goblet-fill', 'color' => '#9C27B0', 'type' => 'expense'],
            ['name' => 'Compras', 'icon' => 'ri-shopping-bag-3-fill', 'color' => '#3F51B5', 'type' => 'expense'],
            ['name' => 'Educación', 'icon' => 'ri-book-open-fill', 'color' => '#03A9F4', 'type' => 'expense'],
            ['name' => 'Finanzas', 'icon' => 'ri-bank-card-fill', 'color' => '#607D8B', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            \App\Models\TransactionCategory::create($cat);
        }
        enableForeignKeys();
    }
}
