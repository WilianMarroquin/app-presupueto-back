<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetItemApiRequest;
use App\Http\Requests\Api\UpdateBudgetItemApiRequest;
use App\Models\BudgetItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetItemApiController
 */
class BudgetItemApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Itemes', only: ['index']),
            new Middleware('permission:Ver Budget Itemes', only: ['show']),
            new Middleware('permission:Crear Budget Itemes', only: ['store']),
            new Middleware('permission:Editar Budget Itemes', only: ['update']),
            new Middleware('permission:Eliminar Budget Itemes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_items.
     * GET|HEAD /budget_items
     */
    public function index(Request $request): JsonResponse
    {
        $budget_items = QueryBuilder::for(BudgetItem::class)
            ->allowedFilters([
    'budget_template_id',
    'transaction_category_id',
    'category_limit',
    'notes'
])
            ->allowedSorts([
    'budget_template_id',
    'transaction_category_id',
    'category_limit',
    'notes'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_items, 'budget_items recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetItem in storage.
     * POST /budget_items
     */
    public function store(CreateBudgetItemApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $budget_items = BudgetItem::create($input);

        return $this->sendResponse($budget_items->toArray(), 'BudgetItem creado con éxito.');
    }

    /**
     * Display the specified BudgetItem.
     * GET|HEAD /budget_items/{id}
     */
    public function show(BudgetItem $budgetitem)
    {
        return $this->sendResponse($budgetitem->toArray(), 'BudgetItem recuperado con éxito.');
    }

    /**
    * Update the specified BudgetItem in storage.
    * PUT/PATCH /budget_items/{id}
    */
    public function update(UpdateBudgetItemApiRequest $request, $id): JsonResponse
    {
        $budgetitem = BudgetItem::findOrFail($id);
        $budgetitem->update($request->validated());
        return $this->sendResponse($budgetitem, 'BudgetItem actualizado con éxito.');
    }

    /**
    * Remove the specified BudgetItem from storage.
    * DELETE /budget_items/{id}
    */
    public function destroy(BudgetItem $budgetitem): JsonResponse
    {
        $budgetitem->delete();
        return $this->sendResponse(null, 'BudgetItem eliminado con éxito.');
    }
}
