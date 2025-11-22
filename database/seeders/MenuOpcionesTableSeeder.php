<?php

namespace Database\Seeders;

use App\Models\MenuOpcion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuOpcionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        disableForeignKeys();
        MenuOpcion::truncate();

        MenuOpcion::create([
            "titulo" => "Create Transacción",
            "icono" => "ri-file-add-line",
            "ruta" => "index",
            "orden" => 0,
            "action" => "Crear Transactiones",
            "subject" => "Transaction",
            "parent_id" => null
        ]);
        MenuOpcion::create([
            "titulo" => "Dashboard",
            "icono" => "ri-dashboard-line",
            "ruta" => "dashboard",
            "orden" => 1,
            "action" => "Listar Dashboard",
            "subject" => "Transaction",
            "parent_id" => null
        ]);
        MenuOpcion::create([
            "titulo" => "Transactions",
            "icono" => "ri-transaction-line",
            "ruta" => "transactions",
            "orden" => 2,
            "action" => "Listar Transactiones",
            "subject" => "Transaction",
            "parent_id" => null
        ]);

        enableForeignKeys();

    }
}
