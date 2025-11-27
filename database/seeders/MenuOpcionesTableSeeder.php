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
            "titulo" => "Home",
            "icono" => "ri-file-add-line",
            "ruta" => "index",
            "orden" => 0,
            "action" => "Home",
            "subject" => "Home",
            "parent_id" => null
        ]);
        MenuOpcion::create([
            "titulo" => "Transactions",
            "icono" => "ri-exchange-line",
            "ruta" => "transactions",
            "orden" => 1,
            "action" => "Listar Transactiones",
            "subject" => "Transaction",
            "parent_id" => null
        ]);
        MenuOpcion::create([
            "titulo_seccion" => "Analytics",
            "icono" => null,
            "ruta" => null,
            "orden" => 2,
            "action" => "Listar Analytics",
            "subject" => "Dashboard",
            "parent_id" => null
        ]);
        MenuOpcion::create([
            "titulo" => "Dashboard",
            "icono" => "ri-dashboard-line",
            "ruta" => "dashboard",
            "orden" => 3,
            "action" => "Listar Dashboard",
            "subject" => "Transaction",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo_seccion" => "Accounts Management",
            "icono" => null,
            "ruta" => null,
            "orden" => 4,
            "action" => "Listar Cuentas Bancarias",
            "subject" => "Accounts",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "My Bank Accounts",
            "icono" => "ri-bank-line",
            "ruta" => "accounts",
            "orden" => 5,
            "action" => "Listar Cuentas Bancarias",
            "subject" => "Transaction",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Installment Plan",
            "icono" => "ri-calendar-check-line",
            "ruta" => "installment-plans",
            "orden" => 6,
            "action" => "Listar Planes de Pago",
            "subject" => "InstallmentPlan",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Categories",
            "icono" => "ri-folder-line",
            "ruta" => "transaction-categories",
            "orden" => 6,
            "action" => "Listar Categorías",
            "subject" => "TransactionCategory",
            "parent_id" => null
        ]);

        MenuOpcion::create([
            "titulo" => "Credit Cards",
            "icono" => "ri-credit-card-line",
            "ruta" => "credit-cards",
            "orden" => 7,
            "action" => "Listar Credit Card",
            "subject" => "Account",
            "parent_id" => null
        ]);

        enableForeignKeys();

    }
}
