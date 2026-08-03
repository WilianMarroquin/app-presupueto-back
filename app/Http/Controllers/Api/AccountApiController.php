<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAccountApiRequest;
use App\Http\Requests\Api\UpdateAccountApiRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use ZipStream\Test\DataDescriptorTest;

/**
 * Class AccountApiController
 */
class AccountApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Accountes', only: ['index']),
            new Middleware('permission:Ver Accountes', only: ['show']),
            new Middleware('permission:Crear Accountes', only: ['store']),
            new Middleware('permission:Editar Accountes', only: ['update']),
            new Middleware('permission:Eliminar Accountes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Accounts.
     * GET|HEAD /accounts
     */
    public function index(Request $request): JsonResponse
    {
        $accounts = QueryBuilder::for(Account::class)
            ->allowedFilters([
                'name',
                'type_id',
                'currency_id',
                'initial_balance',
                'current_balance',
                'is_active',
                AllowedFilter::scope('onlyWithPermittedMovementId', 'onlyWithPermittedMovementId'),
                AllowedFilter::scope('withoutCreditCard', 'withoutCreditCard'),
                AllowedFilter::scope('excludedAccountId', 'excludedAccountId'),
            ])
            ->allowedSorts([
                'name',
                'type_id',
                'currency_id',
                'initial_balance',
                'current_balance',
                'is_active'
            ])
            ->allowedIncludes([
                'type',
                'currency',
                'creditCardDetail'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size') ?? 10);

        return $this->sendResponse($accounts, 'accounts recuperados con éxito.');
    }


    /**
     * Store a newly created Account in storage.
     * POST /accounts
     */
    public function store(CreateAccountApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['user_id'] = usuarioAutenticado()->id;
        $input['current_balance'] = $input['initial_balance'];
        $input['is_transactional'] = false;
        $input['is_active'] = true;

        $accounts = Account::create($input);

        return $this->sendResponse($accounts->toArray(), 'Account creado con éxito.');
    }

    /**
     * Display the specified Account.
     * GET|HEAD /accounts/{id}
     */
    public function show(Account $account)
    {
        $account->load([
            'transactionsPending.category',
            'creditCardDetail',
            'currency',
        ]);

        return $this->sendResponse($account->toArray(), 'Account recuperado con éxito.');
    }

    /**
     * Update the specified Account in storage.
     * PUT/PATCH /accounts/{id}
     */
    public function update(UpdateAccountApiRequest $request, $id): JsonResponse
    {
        if($request->is_transactional) {
            $accountOfUser = Account::whereUserId($request->user_id)->get();

            foreach ($accountOfUser as $account) {
                $account->update([
                    'is_transactional' => false
                ]);
            }
        }

        $account = Account::findOrFail($id);
        $account->update($request->validated());
        return $this->sendResponse($account, 'Account actualizado con éxito.');
    }

    /**
     * Remove the specified Account from storage.
     * DELETE /accounts/{id}
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();
        return $this->sendResponse(null, 'Account eliminado con éxito.');
    }

    public function transferir(Request $request)
    {

        $validated = $request->validate([
            'account_origen_id' => 'required|exists:accounts,id',
            'account_destino_id' => 'required|exists:accounts,id|different:account_origen_id',
            'ammount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string',
        ]);

        $createTransactionService = new CreateTransactionService();

        try {
            DB::beginTransaction();

            $amount = (float) $validated['ammount'];
            $comment = $validated['comment'] ?? 'Transferencia entre cuentas';

            $datosOrigen = [
                'account_id' => $validated['account_origen_id'],
                'amount' => $amount,
                'description' => 'Transferencia enviada: ' . $comment,
                'payment_method_id' => TransactionPaymentMethod::TRANSFERENCIA,
                'category_id' => TransactionCategory::RETIRO_TRASPASO_SALIDA,
            ];

            $dpoOrigen = TransactionDTO::fromArray($datosOrigen);
            $respuestaOrigen = $createTransactionService->execute($dpoOrigen);

            if (!$respuestaOrigen['success']) {
                DB::rollBack();
                return $this->sendError($respuestaOrigen['message'], 500);
            }

            $datosDestino = [
                'account_id' => $validated['account_destino_id'],
                'amount' => $amount,
                'description' => 'Transferencia recibida: ' . $comment,
                'payment_method_id' => TransactionPaymentMethod::TRANSFERENCIA,
                'category_id' => TransactionCategory::DEPOSITO_TRASPASO_ENTRADA
            ];

            $dpoDestino = TransactionDTO::fromArray($datosDestino);
            $respuestaDestino = $createTransactionService->execute($dpoDestino);

            if (!$respuestaDestino['success']) {
                DB::rollBack();
                return $this->sendError($respuestaDestino['message'], 500);
            }

            DB::commit();

            return $this->sendResponse([
                'origen' => $respuestaOrigen['transaction'],
                'destino' => $respuestaDestino['transaction']
            ], 'Transferencia realizada con éxito.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Error al realizar la transferencia: ' . $th->getMessage(), 500);
        }
    }
}
