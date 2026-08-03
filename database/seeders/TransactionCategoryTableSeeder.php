<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategoryTableSeeder extends Seeder
{

    public function run()
    {
        disableForeignKeys();
        TransactionCategory::truncate();

        // 1. Creamos primero las categorías principales (Padres)
        $parentCategories = [
            // --- INGRESOS ---
            [
                'id' => TransactionCategory::SALARIO,
                'name' => 'Salario',
                'type' => 'Income',
                'icon' => 'ri-briefcase-4-fill',
                'color' => '#4CAF50',
                'is_budgetable' => false,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::NEGOCIOS,
                'name' => 'Negocios & Freelance',
                'type' => 'Income',
                'icon' => 'ri-rocket-2-fill',
                'color' => '#2196F3',
                'is_budgetable' => false,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::INVERSIONES,
                'name' => 'Rendimientos & Inversiones',
                'type' => 'Income',
                'icon' => 'ri-line-chart-fill',
                'color' => '#9C27B0',
                'is_budgetable' => false,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::OTROS_INGRESOS,
                'name' => 'Otros Ingresos',
                'type' => 'Income',
                'icon' => 'ri-gift-2-fill',
                'color' => '#FFC107',
                'is_budgetable' => false,
                'parent_id' => null,
            ],

            // --- GASTOS ---
            [
                'id' => TransactionCategory::VIVIENDA,
                'name' => 'Vivienda & Servicios',
                'type' => 'Expense',
                'icon' => 'ri-home-4-fill',
                'color' => '#009688',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::ALIMENTACION,
                'name' => 'Alimentación',
                'type' => 'Expense',
                'icon' => 'ri-restaurant-2-fill',
                'color' => '#FF5722',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::TRANSPORTE,
                'name' => 'Transporte',
                'type' => 'Expense',
                'icon' => 'ri-car-fill',
                'color' => '#F44336',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::SALUD_Y_BIENESTAR,
                'name' => 'Salud & Bienestar',
                'type' => 'Expense',
                'icon' => 'ri-heart-pulse-fill',
                'color' => '#E91E63',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::OCIO_Y_SOCIAL,
                'name' => 'Ocio & Social',
                'type' => 'Expense',
                'icon' => 'ri-goblet-fill',
                'color' => '#9C27B0',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::COMPRAS,
                'name' => 'Compras & Shopping',
                'type' => 'Expense',
                'icon' => 'ri-shopping-bag-3-fill',
                'color' => '#3F51B5',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::EDUCACION,
                'name' => 'Educación',
                'type' => 'Expense',
                'icon' => 'ri-book-open-fill',
                'color' => '#03A9F4',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::GASTOS_FINANCIEROS,
                'name' => 'Gastos Financieros',
                'type' => 'Expense',
                'icon' => 'ri-bank-line',
                'color' => '#607D8B',
                'is_budgetable' => true,
                'parent_id' => null,
            ],
            [
                'id' => TransactionCategory::TECNOLOGIA_Y_SUSCRIPCIONES,
                'name' => 'Tecnología & Suscripciones',
                'type' => 'Expense',
                'icon' => 'ri-macbook-line',
                'color' => '#607D8B',
                'is_budgetable' => true,
                'parent_id' => null,
            ],

            // --- CATEGORÍA PADRE DE TRANSFERENCIAS ---
            [
                'id' => TransactionCategory::TRANSFERENCIAS,
                'name' => 'Transferencias',
                'type' => 'Transfer',
                'icon' => 'ri-exchange-dollar-line',
                'color' => '#795548',
                'is_budgetable' => false,
                'parent_id' => null,
            ],
        ];

        // Guardamos las categorías principales y mapeamos sus IDs por nombre
        $createdCategories = [];
        foreach ($parentCategories as $cat) {
            $created = TransactionCategory::firstOrCreate(
                ['name' => $cat['name']],
                $cat
            );
            $createdCategories[$cat['name']] = $created->id;
        }

        // 2. Definimos las Categorías Hijas (Subcategorías) que dependen de "TransactionCategory"
        $transferenciaParentId = $createdCategories['Transferencias'] ?? TransactionCategory::TRANSFERENCIAS;

        $subCategories = [
            [
                'id' => TransactionCategory::RETIRO_TRASPASO_SALIDA,
                'name' => 'Retiro / Traspaso Salida',
                'type' => 'Expense',
                'icon' => 'ri-arrow-up-circle-fill',
                'color' => '#D32F2F',
                'is_budgetable' => false, // Es operativo interno, no se presupuesta
                'parent_id' => $transferenciaParentId,
            ],
            [
                'id' => TransactionCategory::DEPOSITO_TRASPASO_ENTRADA,
                'name' => 'Depósito / Traspaso Entrada',
                'type' => 'Income',
                'icon' => 'ri-arrow-down-circle-fill',
                'color' => '#388E3C',
                'is_budgetable' => false,
                'parent_id' => $transferenciaParentId,
            ],
            [
                'id' => TransactionCategory::PAGO_TARJETA_CREDITO,
                'name' => 'Pago de Tarjeta de Crédito',
                'type' => 'Expense',
                'icon' => 'ri-bank-card-fill',
                'color' => '#E64A19',
                'is_budgetable' => false, // Opcional, usualmente no lleva límite de presupuesto directo
                'parent_id' => $transferenciaParentId,
            ],
            [
                'id' => TransactionCategory::ENVIO_TERCEROS,
                'name' => 'Envío a Terceros',
                'type' => 'Expense',
                'icon' => 'ri-send-plane-fill',
                'color' => '#C2185B',
                'is_budgetable' => false, // Dinero enviado a otros, no es tu gasto corriente
                'parent_id' => $transferenciaParentId,
            ],
            [
                'id' => TransactionCategory::RECEPCION_TERCEROS,
                'name' => 'Recepción de Terceros',
                'type' => 'Income',
                'icon' => 'ri-download-cloud-fill',
                'color' => '#00796B',
                'is_budgetable' => false,
                'parent_id' => $transferenciaParentId,
            ],
            [
                'id' => TransactionCategory::AHORRO_Y_METAS,
                'name' => 'Ahorro & Metas',
                'type' => 'Expense',
                'icon' => 'ri-safe-2-fill',
                'color' => '#00BCD4',
                'is_budgetable' => true, // ¡Este sí se presupuesta para fijar la meta mensual!
                'parent_id' => $transferenciaParentId,
            ],
        ];

        // Insertamos las subcategorías asegurando su relación
        foreach ($subCategories as $subCat) {
            TransactionCategory::firstOrCreate(
                ['name' => $subCat['name']],
                $subCat
            );
        }

        enableForeignKeys();
    }
}
