<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PruebasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebas:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // ... (resto de las definiciones de la clase)

    public function handle()
    {
        $datos = $this->readExcel();
        $sqlQueries = []; // <--- 1. Array para guardar los queries
        $now = now()->toDateTimeString(); // <--- 2. Obtener el timestamp actual

        foreach ($datos as $index => $row) {
            if($index > 0 && $index < 66) {
                // ... (Lógica de extracción de datos)
                $dateTransaction = $this->getTransactionDate($row);
                $type = ($row[1] == 'Ingreso') ? 'Income' : 'Expense';
                $amount = $row[2];
                // Escapar la descripción para SQL (Importante para evitar errores)
                $description = addslashes($row[3]);
                $product = $row[4];
                $category = $row[5];
                $isRecurring = !($row[7] == 'Variable');

                $categoryId = $this->getCategory($category);
                $paymentMethodId = 1; // TransactionPaymentMethod::EFECTIVO

                // 3. CONSTRUCCIÓN DEL QUERY SQL COMO CADENA DE TEXTO
                $sql = "INSERT INTO `transactions` (`category_id`, `transaction_date`, `amount`, `account_id`, `description`, `is_recurring`, `payment_method_id`, `updated_at`, `created_at`) VALUES (";

                // Valores a insertar
                $sql .= $categoryId . ", ";
                $sql .= "'" . $dateTransaction . "', ";
                $sql .= $amount . ", ";
                $sql .= "'" . 1 . "', ";
                $sql .= "'" . $description . "', ";
                $sql .= (int)$isRecurring . ", ";
                $sql .= $paymentMethodId . ", ";
                $sql .= "'" . $now . "', ";
                $sql .= "'" . $now . "'";

                $sql .= ");";

                $sqlQueries[] = $sql; // Añadir el query al array

                // Eliminamos el Transaction::create para evitar la ejecución
                /*
                Transaction::create([
                    'category_id' => $categoryId,
                    // ...
                ]);
                */
            }
        }

        // 4. IMPRIMIR TODOS LOS QUERIES JUNTOS
        $this->info("--- QUERIES SQL GENERADOS ---");
        $this->line(implode("\n", $sqlQueries));
        $this->info("-----------------------------");

        return 0;

    }
// ... (resto de las funciones)

    public function readExcel(): array
    {

        $ruta = storage_path('app/presupuesto.xlsx');

        // Opción B: Si es una carga única y rápida, verifica que el archivo exista
        if (!file_exists($ruta)) {
            abort(404, 'El archivo no existe en la ruta especificada');
        }

        $datos = Excel::toArray((object)[], $ruta);

        return $datos[1];
    }

    public function getTransactionDate($fila): string
    {
        $fechaTransaccion = $fila[0];
        $fechaPhp = Date::excelToDateTimeObject($fechaTransaccion);

        return $fechaPhp->format('Y-m-d H:i:s');
    }

    public function getCategory($category): int
    {
        if($category == 'Servicios Digitales / TI') {
            return 18;
        }

        if($category == 'Transporte') {
            return 26;
        }

        if($category == 'Alimentos y Hogar') {
            return 23;
        }

        if($category == 'Entretenimiento') {
            return 41;
        }

        if($category == 'Otras Personales') {
            return 56;
        }
        if($category == 'Salud y Bienestar') {
            return 42;
        }

        if($category == 'Salario') {
            return 60;
        }

        return 1;

    }

}

