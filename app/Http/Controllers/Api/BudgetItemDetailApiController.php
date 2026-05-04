<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetItemDetailApiRequest;
use App\Http\Requests\Api\UpdateBudgetItemDetailApiRequest;
use App\Models\BudgetItemDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetItemDetailApiController
 */
class BudgetItemDetailApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Item Detailes', only: ['index']),
            new Middleware('permission:Ver Budget Item Detailes', only: ['show']),
            new Middleware('permission:Crear Budget Item Detailes', only: ['store']),
            new Middleware('permission:Editar Budget Item Detailes', only: ['update']),
            new Middleware('permission:Eliminar Budget Item Detailes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_item_details.
     * GET|HEAD /budget_item_details
     */
    public function index(Request $request): JsonResponse
    {
        $budget_item_details = QueryBuilder::for(BudgetItemDetail::class)
            ->allowedFilters([
                'budget_item_id',
                'name',
                'amount'
            ])
            ->allowedSorts([
                'budget_item_id',
                'name',
                'amount'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_item_details, 'budget_item_details recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetItemDetail in storage.
     * POST /budget_item_details
     */
    public function store(CreateBudgetItemDetailApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $budget_item_details = BudgetItemDetail::create($input);

        return $this->sendResponse($budget_item_details->toArray(), 'BudgetItemDetail creado con éxito.');
    }

    /**
     * Display the specified BudgetItemDetail.
     * GET|HEAD /budget_item_details/{id}
     */
    public function show(BudgetItemDetail $budgetitemdetail)
    {
        return $this->sendResponse($budgetitemdetail->toArray(), 'BudgetItemDetail recuperado con éxito.');
    }

    /**
     * Update the specified BudgetItemDetail in storage.
     * PUT/PATCH /budget_item_details/{id}
     */
    public function update(UpdateBudgetItemDetailApiRequest $request, $id): JsonResponse
    {
        $budgetitemdetail = BudgetItemDetail::findOrFail($id);
        $budgetitemdetail->update($request->validated());
        return $this->sendResponse($budgetitemdetail, 'BudgetItemDetail actualizado con éxito.');
    }

    /**
     * Remove the specified BudgetItemDetail from storage.
     * DELETE /budget_item_details/{id}
     */
    public function destroy(BudgetItemDetail $budgetitemdetail): JsonResponse
    {
        $budgetitemdetail->delete();
        return $this->sendResponse(null, 'BudgetItemDetail eliminado con éxito.');
    }
}
