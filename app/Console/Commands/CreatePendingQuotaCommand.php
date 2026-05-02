<?php

namespace App\Console\Commands;

use App\Models\InstallmentPlan;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreatePendingQuotaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-pending-quota-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $installmentsPendings = InstallmentPlan::where('status', InstallmentPlan::STATUS_ACTIVE)
            ->withCount('payments')
            ->get();

        $createTransactionService = new CreateTransactionService();

        foreach ($installmentsPendings as $plan) {
            if ($plan->payments_count >= $plan->total_installments) {
                $plan->update(['status' => InstallmentPlan::STATUS_COMPLETED]);
                continue;
            }

            $nextInstallmentNumber = $plan->payments_count + 1;

            $datos = [
                'account_id' => $plan->account_id,
                'amount' => $plan->monthly_fee,
                'description' => 'VisaCuota ' . $plan->name . ' N° ' . $nextInstallmentNumber,
                'payment_method_id' => TransactionPaymentMethod::TRANSFERENCIA,
                'category_id' => TransactionCategory::GASTOS_FINANCIEROS,
            ];

            $dpo = TransactionDTO::fromArray($datos);

            $respuesta = $createTransactionService->execute($dpo);

            if (!$respuesta['success']) {
                $this->error('Error al crear la cuota: ' . $respuesta['message']);
                continue;
            }

            /** * PASO CLAVE: ASOCIAR EL PAGO (ATTACH)
             * Recuperamos el modelo Transaction creado desde la respuesta del servicio
             */
            $transaction = $respuesta['transaction']; // Asegúrate que tu servicio retorne el modelo aquí

            $plan->payments()->attach($transaction['id'], [
                'installment_number' => $nextInstallmentNumber,
                'amount'             => $plan->monthly_fee
            ]);

        }
//        return Command::SUCCESS;
    }
}
