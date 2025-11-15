<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateTransactionPaymentMethodApiRequest;
use App\Http\Requests\Api\UpdateTransactionPaymentMethodApiRequest;
use App\Models\TransactionPaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TransactionPaymentMethodApiController
 */
class TransactionPaymentMethodApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Transaction Payment Methodes', only: ['index']),
            new Middleware('permission:Ver Transaction Payment Methodes', only: ['show']),
            new Middleware('permission:Crear Transaction Payment Methodes', only: ['store']),
            new Middleware('permission:Editar Transaction Payment Methodes', only: ['update']),
            new Middleware('permission:Eliminar Transaction Payment Methodes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Transaction_payment_methods.
     * GET|HEAD /transaction_payment_methods
     */
    public function index(Request $request): JsonResponse
    {
        $transaction_payment_methods = QueryBuilder::for(TransactionPaymentMethod::class)
            ->allowedFilters([
    'name'
])
            ->allowedSorts([
    'name'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($transaction_payment_methods, 'transaction_payment_methods recuperados con éxito.');
    }


    /**
     * Store a newly created TransactionPaymentMethod in storage.
     * POST /transaction_payment_methods
     */
    public function store(CreateTransactionPaymentMethodApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $transaction_payment_methods = TransactionPaymentMethod::create($input);

        return $this->sendResponse($transaction_payment_methods->toArray(), 'TransactionPaymentMethod creado con éxito.');
    }

    /**
     * Display the specified TransactionPaymentMethod.
     * GET|HEAD /transaction_payment_methods/{id}
     */
    public function show(TransactionPaymentMethod $transactionpaymentmethod)
    {
        return $this->sendResponse($transactionpaymentmethod->toArray(), 'TransactionPaymentMethod recuperado con éxito.');
    }

    /**
    * Update the specified TransactionPaymentMethod in storage.
    * PUT/PATCH /transaction_payment_methods/{id}
    */
    public function update(UpdateTransactionPaymentMethodApiRequest $request, $id): JsonResponse
    {
        $transactionpaymentmethod = TransactionPaymentMethod::findOrFail($id);
        $transactionpaymentmethod->update($request->validated());
        return $this->sendResponse($transactionpaymentmethod, 'TransactionPaymentMethod actualizado con éxito.');
    }

    /**
    * Remove the specified TransactionPaymentMethod from storage.
    * DELETE /transaction_payment_methods/{id}
    */
    public function destroy(TransactionPaymentMethod $transactionpaymentmethod): JsonResponse
    {
        $transactionpaymentmethod->delete();
        return $this->sendResponse(null, 'TransactionPaymentMethod eliminado con éxito.');
    }
}
