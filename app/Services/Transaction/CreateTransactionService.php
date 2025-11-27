<?php

namespace App\Services\Transaction;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\AIService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Client\RequestException;

class CreateTransactionService
{

    public function execute(TransactionDTO $data): array
    {

        try {
            $category = $this->getCategory($data->description);
            DB::beginTransaction();

            $account = Account::findOrFail($data->account_id);
            $payload = $data->toArray();
            $payload['transaction_date'] = now();
            $payload['category_id'] = $category->id;
            $payload['is_settled'] = ($account->nature === 'asset');

            $transaction = Transaction::create($payload);

            if($transaction->category->isExpense()){
                $account->withdraw($transaction->amount);
            }
            if($transaction->category->isIncome()){
                $account->depositary($transaction->amount);
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error al crear la transacción: ' . $e->getMessage(),
            ];
        }
        return [
            'success' => true,
            'transaction' => $transaction->toArray(),
            'message' => 'Transacción creada con éxito.',
        ];

    }



    public function getCategory(string $description): TransactionCategory
    {
        $categories = TransactionCategory::pluck('name')->toArray();
        $aiService = new AIService();

        try {
            $aiResult = $aiService->categorize($description, $categories);

        } catch (RequestException $e) {
            // CAPTURAMOS EL ERROR 429 (Límite de la API)
            if ($e->response->status() === 429) {
                throw new \Exception("Espera unos segundos antes de intentarlo (Límite de IA alcanzado).");
            }

            // Otros errores de conexión (500, 404, DNS)
            Log::error("Error IA: " . $e->getMessage());
            throw new \Exception("El servicio de categorización no está disponible momentáneamente.");
        }

        // SI LA IA RESPONDE, PERO NO ENTIENDE (Respuesta vacía o inválida)
        if (empty($aiResult) || !isset($aiResult['category'])) {
            // Usamos ValidationException para que el Frontend marque el campo en rojo
            throw ValidationException::withMessages([
                'description' => 'Da más detalles para identificar la categoría (La IA no entendió).'
            ]);
        }

        // BUSCAMOS LA CATEGORÍA
        $category = TransactionCategory::where('name', $aiResult['category'])->first();

        // SI LA IA ALUCINÓ UNA CATEGORÍA QUE NO EXISTE
        if (!$category) {
            throw ValidationException::withMessages([
                'description' => "No pude asociar '{$description}' a ninguna categoría válida. Intenta ser más específico."
            ]);
        }

        // ÉXITO: Sincronizamos tags y retornamos
        if (!empty($aiResult['tags'])) {
            $category->syncKeywords($aiResult['tags']);
        }

        return $category;
    }

}
