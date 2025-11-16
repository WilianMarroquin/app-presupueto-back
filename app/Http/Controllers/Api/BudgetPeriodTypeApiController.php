<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetPeriodTypeApiRequest;
use App\Http\Requests\Api\UpdateBudgetPeriodTypeApiRequest;
use App\Models\BudgetPeriodType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetPeriodTypeApiController
 */
class BudgetPeriodTypeApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Period Types', only: ['index']),
            new Middleware('permission:Ver Budget Period Types', only: ['show']),
            new Middleware('permission:Crear Budget Period Types', only: ['store']),
            new Middleware('permission:Editar Budget Period Types', only: ['update']),
            new Middleware('permission:Eliminar Budget Period Types', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_period_types.
     * GET|HEAD /budget_period_types
     */
    public function index(Request $request): JsonResponse
    {
        $budget_period_types = QueryBuilder::for(BudgetPeriodType::class)
            ->allowedFilters([
    'name',
    'description'
])
            ->allowedSorts([
    'name',
    'description'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_period_types, 'budget_period_types recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetPeriodType in storage.
     * POST /budget_period_types
     */
    public function store(CreateBudgetPeriodTypeApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $budget_period_types = BudgetPeriodType::create($input);

        return $this->sendResponse($budget_period_types->toArray(), 'BudgetPeriodType creado con éxito.');
    }

    /**
     * Display the specified BudgetPeriodType.
     * GET|HEAD /budget_period_types/{id}
     */
    public function show(BudgetPeriodType $budgetperiodtype)
    {
        return $this->sendResponse($budgetperiodtype->toArray(), 'BudgetPeriodType recuperado con éxito.');
    }

    /**
    * Update the specified BudgetPeriodType in storage.
    * PUT/PATCH /budget_period_types/{id}
    */
    public function update(UpdateBudgetPeriodTypeApiRequest $request, $id): JsonResponse
    {
        $budgetperiodtype = BudgetPeriodType::findOrFail($id);
        $budgetperiodtype->update($request->validated());
        return $this->sendResponse($budgetperiodtype, 'BudgetPeriodType actualizado con éxito.');
    }

    /**
    * Remove the specified BudgetPeriodType from storage.
    * DELETE /budget_period_types/{id}
    */
    public function destroy(BudgetPeriodType $budgetperiodtype): JsonResponse
    {
        $budgetperiodtype->delete();
        return $this->sendResponse(null, 'BudgetPeriodType eliminado con éxito.');
    }
}
