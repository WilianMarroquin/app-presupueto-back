<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateTransactionApiRequest;
use App\Http\Requests\Api\UpdateTransactionApiRequest;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TransactionApiController
 */
class TransactionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Transactiones', only: ['index']),
            new Middleware('permission:Ver Transactiones', only: ['show']),
            new Middleware('permission:Crear Transactiones', only: ['store']),
            new Middleware('permission:Editar Transactiones', only: ['update']),
            new Middleware('permission:Eliminar Transactiones', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Transactions.
     * GET|HEAD /transactions
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = QueryBuilder::for(Transaction::class)
            ->allowedFilters([
                'category_id',
                'account_id',
                'amount',
                'description',
                'transaction_date',
                'payment_method_id',
                'is_recurring',
                'notes',
                'created_ad'
            ])
            ->allowedSorts([
                'category_id',
                'account_id',
                'amount',
                'description',
                'transaction_date',
                'payment_method_id',
                'is_recurring',
                'notes',
                'created_ad'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size') ?? 10);

        return $this->sendResponse($transactions, 'transactions recuperados con éxito.');
    }


    /**
     * Store a newly created Transaction in storage.
     * POST /transactions
     */
    public function store(CreateTransactionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['transaction_date'] = now();

        $transactions = Transaction::create($input);

        return $this->sendResponse($transactions->toArray(), 'Transaction creado con éxito.');
    }

    /**
     * Display the specified Transaction.
     * GET|HEAD /transactions/{id}
     */
    public function show(Transaction $transaction)
    {
        return $this->sendResponse($transaction->toArray(), 'Transaction recuperado con éxito.');
    }

    /**
     * Update the specified Transaction in storage.
     * PUT/PATCH /transactions/{id}
     */
    public function update(UpdateTransactionApiRequest $request, $id): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->validated());
        return $this->sendResponse($transaction, 'Transaction actualizado con éxito.');
    }

    /**
     * Remove the specified Transaction from storage.
     * DELETE /transactions/{id}
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();
        return $this->sendResponse(null, 'Transaction eliminado con éxito.');
    }
}
