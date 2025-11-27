<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StoreCreditCardRequest;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\CreditCardDetail;
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
            new Middleware('permission:Listar Credit Card', only: ['index']),
            new Middleware('permission:Ver Credit Card', only: ['show']),
            new Middleware('permission:Crear Credit Card', only: ['store']),
            new Middleware('permission:Editar Credit Card', only: ['update']),
            new Middleware('permission:Eliminar Credit Card', only: ['destroy']),
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
                ]);

                // 2. Crear el Detalle (El Hijo)
                // Usamos la relación para crear. Laravel inyecta el account_id solo.
                return $account->creditCardDetail()->create([
                    'bank_name' => $request->bank_name,
                    'alias' => $request->alias,
                    'network' => $request->network,
                    'color' => $request->color,
                    'last_4' => $request->last_4,
                    'cutoff_day' => $request->cutoff_day,
                    'payment_day' => $request->payment_day,
                ]);
            });

            // Si llegamos aquí, todo se guardó perfecto.
            return $this->sendResponse($creditCardDetail->toArray(), 'Tarjeta creada con éxito.');

        } catch (\Exception $e) {
            // Si algo falló, Laravel ya deshizo los cambios en la DB automáticamente.
            // Aquí logueas el error y respondes feo al front.
            \Log::error("Error creando TC: " . $e->getMessage());
            return $this->sendError('Error al crear la tarjeta.', [], 500);
        }
    }

    public function update(UpdateCreditCardProvisionsApiRequest $request, $id): JsonResponse
    {
        $creditcardprovisions = CreditCardProvisions::findOrFail($id);
        $creditcardprovisions->update($request->validated());
        return $this->sendResponse($creditcardprovisions, 'CreditCardProvisions actualizado con éxito.');
    }

}
