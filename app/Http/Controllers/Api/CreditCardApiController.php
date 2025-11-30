<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StoreCreditCardRequest;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\CreditCardDetail;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateCreditCardProvisionsApiRequest;
use App\Http\Requests\Api\UpdateCreditCardProvisionsApiRequest;
use App\Models\CreditCardProvisions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CreditCardProvisionsApiController
 */
class CreditCardApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
//            new Middleware('permission:Listar Credit Card', only: ['index']),
//            new Middleware('permission:Ver Credit Card', only: ['show']),
            new Middleware('permission:Crear Credit Card', only: ['store']),
//            new Middleware('permission:Editar Credit Card', only: ['update']),
//            new Middleware('permission:Eliminar Credit Card', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Credit_card_provisions.
     * GET|HEAD /credit_card_provisions
     */



    /**
     * Store a newly created CreditCardProvisions in storage.
     * POST /credit_card_provisions
     */
    public function store(StoreCreditCardRequest $request): JsonResponse
    {
        try {
            // INICIO DE LA TRANSACCIÓN (El Pacto de Sangre)
            $creditCardDetail = DB::transaction(function () use ($request) {

                // 1. Crear la Cuenta Base (El Padre)
                $account = Account::create([
                    'name' => $request->alias,
                    'type_id' => AccountType::CREDIT_CARD, // Sugerencia: Usa Enums si puedes
                    'currency_id' => $request->currency_id,
                    'initial_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                    'nature' => 'liability', // Pasivo
                    'description' => $request->description
                ]);

                // 2. Crear el Detalle (El Hijo)
                // Usamos la relación para crear. Laravel inyecta el account_id solo.
                return $account->creditCardDetail()->create([
                    'alias' => $request->alias,
                    'network' => $request->network,
                    'color' => $request->color,
                    'last_4' => $request->last_4,
                    'cutoff_day' => $request->cutoff_day,
                    'payment_day' => $request->payment_day,
                    'credit_limit' => $request->credit_limit,
                ]);
            });

            // Si llegamos aquí, todo se guardó perfecto.
            return $this->sendResponse($creditCardDetail->toArray(), 'Tarjeta creada con éxito.');

        } catch (\Exception $e) {
            // Si algo falló, Laravel ya deshizo los cambios en la DB automáticamente.
            // Aquí logueas el error y respondes feo al front.
            \Log::error("Error creando TC: " . $e->getMessage());
            return $this->sendError('Error al crear la tarjeta.'. $e->getMessage(), 500);
        }
    }

    public function update(UpdateCreditCardProvisionsApiRequest $request, $id): JsonResponse
    {
        $creditcardprovisions = CreditCardProvisions::findOrFail($id);
        $creditcardprovisions->update($request->validated());
        return $this->sendResponse($creditcardprovisions, 'CreditCardProvisions actualizado con éxito.');
    }

    public function payment(Request $request): JsonResponse
    {
        $request->validate([
            'credit_card_id' => 'required|exists:accounts,id',
            'from_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $account = Account::findOrFail($request->credit_card_id);
        $createTransactionService = new CreateTransactionService();

        try {
            DB::beginTransaction();
            $account->current_balance = 0;
            $account->save();

            foreach ($account->transactionsPending as $transaction) {
                $transaction->is_settled = 1;
                $transaction->save();
            }

            $datos = [
                'account_id' => $request->from_account_id,
                'amount' => $request->amount,
                'description' => 'Pago de tarjeta de crédito: ' . $account->name,
                'payment_method_id' => TransactionPaymentMethod::TRANSFERENCIA,
                'category_id' => TransactionCategory::PAGOS_TC,
            ];

            $dpo = TransactionDTO::fromArray($datos);
            $respuesta = $createTransactionService->execute($dpo);

            if (!$respuesta['success']) {
                DB::rollBack();
                return $this->sendError($respuesta['message'], 500);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Error al procesar el pago: ' . $th->getMessage(), 500);
        }

        return $this->sendSuccess('Pago realizado con éxito.');

    }

}
