<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Transaction;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateTransactionCategoryApiRequest;
use App\Http\Requests\Api\UpdateTransactionCategoryApiRequest;
use App\Models\TransactionCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TransactionCategoryApiController
 */
class TransactionCategoryApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Transaction Categoryes', only: ['index']),
            new Middleware('permission:Ver Transaction Categoryes', only: ['show']),
            new Middleware('permission:Crear Transaction Categoryes', only: ['store']),
            new Middleware('permission:Editar Transaction Categoryes', only: ['update']),
            new Middleware('permission:Eliminar Transaction Categoryes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Transaction_categories.
     * GET|HEAD /transaction_categories
     */
    public function index(Request $request): JsonResponse
    {
        $transaction_categories = QueryBuilder::for(TransactionCategory::class)
            ->allowedFilters([
                'nombre',
                'type',
                'description',
            ])
            ->allowedSorts([
                'nombre',
                'type',
                'description',
            ])
            ->defaultSort('-id')
            ->jsonPaginate(request('per_page') ?? 10);

        return $this->sendResponse($transaction_categories, 'Categories retrieved successfully');
    }


    /**
     * Store a newly created TransactionCategory in storage.
     * POST /transaction_categories
     */
    public function store(CreateTransactionCategoryApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $transaction_categories = TransactionCategory::create($input);

        return $this->sendResponse($transaction_categories->toArray(), 'TransactionCategory creado con éxito.');
    }

    /**
     * Display the specified TransactionCategory.
     * GET|HEAD /transaction_categories/{id}
     */
    public function show(TransactionCategory $transactioncategory)
    {
        return $this->sendResponse($transactioncategory->toArray(), 'TransactionCategory recuperado con éxito.');
    }

    /**
     * Update the specified TransactionCategory in storage.
     * PUT/PATCH /transaction_categories/{id}
     */
    public function update(UpdateTransactionCategoryApiRequest $request, $id): JsonResponse
    {
        $transactioncategory = TransactionCategory::findOrFail($id);
        $transactioncategory->update($request->validated());
        return $this->sendResponse($transactioncategory, 'TransactionCategory actualizado con éxito.');
    }

    /**
     * Remove the specified TransactionCategory from storage.
     * DELETE /transaction_categories/{id}
     */
    public function destroy(TransactionCategory $transactioncategory): JsonResponse
    {
        $transactioncategory->delete();
        return $this->sendResponse(null, 'TransactionCategory eliminado con éxito.');
    }

}
