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
            // --- INGRESOS (Suman) ---
            [
                'name' => 'Salario',
                'type' => 'Income',
                'icon' => 'ri-briefcase-4-fill',
                'color' => '#4CAF50' // Verde Dinero
            ],
            [
                'name' => 'Negocios & Freelance',
                'type' => 'Income',
                'icon' => 'ri-rocket-2-fill',
                'color' => '#2196F3' // Azul Negocios
            ],
            [
                'name' => 'Rendimientos & Inversiones', // Antes parte de Finanzas
                'type' => 'Income',
                'icon' => 'ri-line-chart-fill',
                'color' => '#9C27B0' // Morado Riqueza
            ],
            [
                'name' => 'Otros Ingresos',
                'type' => 'Income',
                'icon' => 'ri-gift-2-fill',
                'color' => '#FFC107' // Amarillo Sorpresa
            ],

            // --- GASTOS (Restan) ---
            [
                'name' => 'Vivienda & Servicios',
                'type' => 'Expense',
                'icon' => 'ri-home-4-fill',
                'color' => '#009688' // Teal
            ],
            [
                'name' => 'Alimentación',
                'type' => 'Expense',
                'icon' => 'ri-restaurant-2-fill',
                'color' => '#FF5722' // Naranja
            ],
            [
                'name' => 'Transporte',
                'type' => 'Expense',
                'icon' => 'ri-car-fill',
                'color' => '#F44336' // Rojo
            ],
            [
                'name' => 'Salud & Bienestar',
                'type' => 'Expense',
                'icon' => 'ri-heart-pulse-fill',
                'color' => '#E91E63' // Rosa
            ],
            [
                'name' => 'Ocio & Social',
                'type' => 'Expense',
                'icon' => 'ri-goblet-fill',
                'color' => '#9C27B0' // Morado
            ],
            [
                'name' => 'Compras & Shopping',
                'type' => 'Expense',
                'icon' => 'ri-shopping-bag-3-fill',
                'color' => '#3F51B5' // Indigo
            ],
            [
                'name' => 'Educación',
                'type' => 'Expense',
                'icon' => 'ri-book-open-fill',
                'color' => '#03A9F4' // Celeste
            ],
            [
                'name' => 'Gastos Financieros', // Intereses, comisiones, seguros de deuda
                'type' => 'Expense',
                'icon' => 'ri-bank-line',
                'color' => '#607D8B' // Gris Blue
            ],

            // --- TRANSFERENCIAS (Neutras) ---
            // Esta es la CLAVE para arreglar tu gráfica
            [
                'name' => 'Pagos de Tarjeta / Transferencias',
                'type' => 'Transfer',
                'icon' => 'ri-exchange-dollar-line',
                'color' => '#795548' // Café / Neutro
            ],
        ];

        foreach ($categories as $cat) {
            TransactionCategory::firstOrCreate(
                ['name' => $cat['name']], // Evita duplicados si corres el seeder 2 veces
                $cat
            );
        }
        enableForeignKeys();
    }
}
