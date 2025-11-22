<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetApiRequest;
use App\Http\Requests\Api\UpdateBudgetApiRequest;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetApiController
 */
class BudgetApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budgetes', only: ['index']),
            new Middleware('permission:Ver Budgetes', only: ['show']),
            new Middleware('permission:Crear Budgetes', only: ['store']),
            new Middleware('permission:Editar Budgetes', only: ['update']),
            new Middleware('permission:Eliminar Budgetes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budgets.
     * GET|HEAD /budgets
     */
    public function index(Request $request): JsonResponse
    {
        $budgets = QueryBuilder::for(Budget::class)
            ->allowedFilters([
                'amount',
                'period_types_id',
                'category_id',
                'start_date',
                'end_date'
            ])
            ->allowedSorts([
                'amount',
                'period_types_id',
                'category_id',
                'start_date',
                'end_date'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(100);

        return $this->sendResponse($budgets, 'budgets recuperados con éxito.');
    }


    /**
     * Store a newly created Budget in storage.
     * POST /budgets
     */
    public function store(CreateBudgetApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $budgets = Budget::create($input);

        return $this->sendResponse($budgets->toArray(), 'Budget creado con éxito.');
    }

    /**
     * Display the specified Budget.
     * GET|HEAD /budgets/{id}
     */
    public function show(Budget $budget)
    {
        return $this->sendResponse($budget->toArray(), 'Budget recuperado con éxito.');
    }

    /**
     * Update the specified Budget in storage.
     * PUT/PATCH /budgets/{id}
     */
    public function update(UpdateBudgetApiRequest $request, $id): JsonResponse
    {
        $budget = Budget::findOrFail($id);
        $budget->update($request->validated());
        return $this->sendResponse($budget, 'Budget actualizado con éxito.');
    }

    /**
     * Remove the specified Budget from storage.
     * DELETE /budgets/{id}
     */
    public function destroy(Budget $budget): JsonResponse
    {
        $budget->delete();
        return $this->sendResponse(null, 'Budget eliminado con éxito.');
    }
}
