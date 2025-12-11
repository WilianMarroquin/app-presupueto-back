<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\AIService; // Importa tu servicio
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessVoiceTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [2, 5, 10];

    protected $text;

    /**
     * Recibimos los datos básicos desde el Controlador.
     */
    public function __construct(string $text, )
    {
        $this->text = $text;
    }

    /**
     * Laravel inyecta automáticamente tu AIService aquí.
     */
    public function handle(AIService $aiService, CreateTransactionService $createTransactionService)
    {

        try {
            DB::beginTransaction();
            $data = $aiService->getDataForVoice($this->text);

            if (!$data) {
                $this->fail(new \Exception("No se pudo extraer datos de la voz"));
                return;
            }

            $datos = [
                'account_id' => $data['account_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'payment_method_id' => $data['payment_method_id'],
                'category_id' => $data['category_id'],
            ];
            $dpo = TransactionDTO::fromArray($datos);

            $respuesta = $createTransactionService->execute($dpo);

            if (!$respuesta['success']) {
                DB::rollBack();
                $this->fail(new \Exception($respuesta['message']));
            }

            Log::info("Transacción creada exitosamente: " . $respuesta['transaction']->id);
            DB::commit();
        } catch (\Exception $e) {
            Log::error("Error crítico en ProcessVoiceTransaction: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
