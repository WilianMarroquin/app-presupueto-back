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
            // ===== CATEGORÍAS DE GASTOS =====

            // Vivienda
            [
                'name' => 'Vivienda',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos relacionados con la vivienda',
            ],

            // Alimentación
            [
                'name' => 'Alimentación',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos de comida y bebidas',
            ],

            // Transporte
            [
                'name' => 'Transporte',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos de movilidad y transporte',
            ],

            // Salud
            [
                'name' => 'Salud',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos médicos y de salud',
            ],

            // Educación
            [
                'name' => 'Educación',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos educativos y de formación',
            ],

            // Entretenimiento
            [
                'name' => 'Entretenimiento',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos de diversión y ocio',
            ],

            // Cuidado Personal
            [
                'name' => 'Cuidado Personal',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos de cuidado e higiene personal',
            ],

            // Seguros
            [
                'name' => 'Seguros',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Pagos de seguros y pólizas',
            ],

            // Deudas y Préstamos
            [
                'name' => 'Deudas y Préstamos',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Pagos de deudas, créditos y préstamos',
            ],
            // Deudas y Préstamos
            [
                'name' => 'Deudas y Préstamos',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Pagos de deudas, créditos y préstamos',
            ],


            // Otros Gastos
            [
                'name' => 'Otros Gastos',
                'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE,
                'parent_id' => null,
                'description' => 'Gastos varios no clasificados',
            ],

            // ===== CATEGORÍAS DE INGRESOS =====

            // Ingresos Fijos
            [
                'name' => 'Ingresos Fijos',
                'type' => TransactionCategory::CATEGORY_TYPE_INCOME,
                'parent_id' => null,
                'description' => 'Ingresos regulares y predecibles',
            ],

            // Ingresos Variables
            [
                'name' => 'Ingresos Variables',
                'type' => TransactionCategory::CATEGORY_TYPE_INCOME,
                'parent_id' => null,
                'description' => 'Ingresos que varían mes a mes',
            ],

            // Ingresos Esporádicos
            [
                'name' => 'Ingresos Esporádicos',
                'type' => TransactionCategory::CATEGORY_TYPE_INCOME,
                'parent_id' => null,
                'description' => 'Ingresos ocasionales no regulares',
            ],

            // Ingresos Pasivos
            [
                'name' => 'Ingresos Pasivos',
                'type' => TransactionCategory::CATEGORY_TYPE_INCOME,
                'parent_id' => null,
                'description' => 'Ingresos generados sin trabajo activo',
            ],
        ];

        // Insertar categorías principales
        TransactionCategory::insert($categories);

        // Obtener IDs de categorías para crear subcategorías
        $vivienda = TransactionCategory::where('name', 'Vivienda')->first();
        $alimentacion = TransactionCategory::where('name', 'Alimentación')->first();
        $transporte = TransactionCategory::where('name', 'Transporte')->first();
        $salud = TransactionCategory::where('name', 'Salud')->first();
        $educacion = TransactionCategory::where('name', 'Educación')->first();
        $entretenimiento = TransactionCategory::where('name', 'Entretenimiento')->first();
        $cuidadoPersonal = TransactionCategory::where('name', 'Cuidado Personal')->first();
        $seguros = TransactionCategory::where('name', 'Seguros')->first();
        $deudas = TransactionCategory::where('name', 'Deudas y Préstamos')->first();
        $ingresosFijos = TransactionCategory::where('name', 'Ingresos Fijos')->first();
        $ingresosVariables = TransactionCategory::where('name', 'Ingresos Variables')->first();
        $ingresosEsporadicos = TransactionCategory::where('name', 'Ingresos Esporádicos')->first();
        $ingresosPasivos = TransactionCategory::where('name', 'Ingresos Pasivos')->first();
        $otrosGastos = TransactionCategory::where('name', 'Otros Gastos')->first();

        $subcategories = [
            // Subcategorías de Vivienda
            ['name' => 'Renta/Hipoteca', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Pago mensual de vivienda'],
            ['name' => 'Servicios', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Luz, agua, gas'],
            ['name' => 'Internet y Teléfono', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Servicios de comunicación'],
            ['name' => 'Mantenimiento', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Reparaciones y mantenimiento'],
            ['name' => 'Impuesto Predial', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Impuestos de propiedad'],
            ['name' => 'Muebles', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $vivienda->id, 'description' => 'Muebles y electrodomésticos'],

            // Subcategorías de Alimentación
            ['name' => 'Supermercado', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $alimentacion->id, 'description' => 'Compras de despensa'],
            ['name' => 'Restaurantes', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $alimentacion->id, 'description' => 'Comidas fuera de casa'],
            ['name' => 'Cafeterías', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $alimentacion->id, 'description' => 'Café y bebidas'],
            ['name' => 'Refacciones', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $alimentacion->id, 'description' => 'Snacks y botanas'],

            // Subcategorías de Transporte
            ['name' => 'Gasolina', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Combustible del vehículo'],
            ['name' => 'Transporte Público', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Metro, autobús, taxi'],
            ['name' => 'Mantenimiento Vehículo', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Reparaciones y servicio'],
            ['name' => 'Estacionamiento', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Pagos de estacionamiento'],
            ['name' => 'Pago de Auto', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Mensualidades del vehículo'],
            ['name' => 'Seguro Vehicular', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $transporte->id, 'description' => 'Seguro del auto'],

            // Subcategorías de Salud
            ['name' => 'Consultas Médicas', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $salud->id, 'description' => 'Visitas al doctor'],
            ['name' => 'Medicamentos', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $salud->id, 'description' => 'Compra de medicinas'],
            ['name' => 'Seguro Médico', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $salud->id, 'description' => 'Seguro de salud'],
            ['name' => 'Dentista', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $salud->id, 'description' => 'Atención dental'],
            ['name' => 'Óptica', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $salud->id, 'description' => 'Lentes y exámenes visuales'],

            // Subcategorías de Educación
            ['name' => 'Colegiaturas', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $educacion->id, 'description' => 'Pagos escolares'],
            ['name' => 'Cursos y Talleres', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $educacion->id, 'description' => 'Educación continua'],
            ['name' => 'Material Escolar', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $educacion->id, 'description' => 'Útiles y materiales'],
            ['name' => 'Libros', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $educacion->id, 'description' => 'Libros educativos'],

            // Subcategorías de Entretenimiento
            ['name' => 'Cine y Teatro', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $entretenimiento->id, 'description' => 'Entretenimiento cultural'],
            ['name' => 'Gimnasio', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $entretenimiento->id, 'description' => 'Membresía deportiva'],
            ['name' => 'Suscripciones', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $entretenimiento->id, 'description' => 'Netflix, Spotify, etc'],
            ['name' => 'Vacaciones', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $entretenimiento->id, 'description' => 'Viajes y turismo'],
            ['name' => 'Hobbies', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $entretenimiento->id, 'description' => 'Pasatiempos diversos'],

            // Subcategorías de Cuidado Personal
            ['name' => 'Ropa y Calzado', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $cuidadoPersonal->id, 'description' => 'Vestimenta'],
            ['name' => 'Peluquería', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $cuidadoPersonal->id, 'description' => 'Cortes y peinados'],
            ['name' => 'Productos de Higiene', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $cuidadoPersonal->id, 'description' => 'Artículos de aseo'],
            ['name' => 'Cosméticos', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $cuidadoPersonal->id, 'description' => 'Maquillaje y cuidado'],

            // Subcategorías de Seguros
            ['name' => 'Seguro de Vida', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $seguros->id, 'description' => 'Póliza de vida'],
            ['name' => 'Seguro del Hogar', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $seguros->id, 'description' => 'Seguro de vivienda'],
            ['name' => 'Otros Seguros', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $seguros->id, 'description' => 'Seguros adicionales'],

            // Subcategorías de Deudas
            ['name' => 'Tarjetas de Crédito', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $deudas->id, 'description' => 'Pagos de tarjetas'],
            ['name' => 'Préstamos Personales', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $deudas->id, 'description' => 'Abonos a préstamos'],
            ['name' => 'Créditos', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $deudas->id, 'description' => 'Otros créditos'],

            // SubCategorías de Otros Gastos
            ['name' => 'Donaciones', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $otrosGastos->id, 'description' => 'Aportaciones benéficas'],
            ['name' => 'Multas', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $otrosGastos->id, 'description' => 'Sanciones y multas'],
            ['name' => 'Gastos Varios', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $otrosGastos->id, 'description' => 'Gastos imprevistos'],
            ['name' => 'Servicios Digitales', 'type' => TransactionCategory::CATEGORY_TYPE_EXPENSE, 'parent_id' => $otrosGastos->id, 'description' => 'Apps y plataformas digitales'],

            // Subcategorías de Ingresos Fijos
            ['name' => 'Salario', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosFijos->id, 'description' => 'Sueldo mensual'],
            ['name' => 'Pensión', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosFijos->id, 'description' => 'Pensión por jubilación'],
            ['name' => 'Renta de Propiedades', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosFijos->id, 'description' => 'Ingresos por rentar'],

            // Subcategorías de Ingresos Variables
            ['name' => 'Comisiones', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosVariables->id, 'description' => 'Comisiones de ventas'],
            ['name' => 'Horas Extras', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosVariables->id, 'description' => 'Pago por horas extra'],
            ['name' => 'Bonos', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosVariables->id, 'description' => 'Bonificaciones'],
            ['name' => 'Propinas', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosVariables->id, 'description' => 'Propinas recibidas'],

            // Subcategorías de Ingresos Esporádicos
            ['name' => 'Venta de Artículos', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosEsporadicos->id, 'description' => 'Ventas ocasionales'],
            ['name' => 'Regalos', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosEsporadicos->id, 'description' => 'Dinero recibido como regalo'],
            ['name' => 'Devolución de Impuestos', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosEsporadicos->id, 'description' => 'Reembolsos fiscales'],
            ['name' => 'Premios', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosEsporadicos->id, 'description' => 'Premios ganados'],

            // Subcategorías de Ingresos Pasivos
            ['name' => 'Dividendos', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosPasivos->id, 'description' => 'Dividendos de inversiones'],
            ['name' => 'Intereses Bancarios', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosPasivos->id, 'description' => 'Intereses generados'],
            ['name' => 'Regalías', 'type' => TransactionCategory::CATEGORY_TYPE_INCOME, 'parent_id' => $ingresosPasivos->id, 'description' => 'Ingresos por regalías'],
        ];

        // Insertar subcategorías
        TransactionCategory::insert($subcategories);

        enableForeignKeys();
    }
}
