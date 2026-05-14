<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetPeriodApiRequest;
use App\Http\Requests\Api\UpdateBudgetPeriodApiRequest;
use App\Models\BudgetPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetPeriodApiController
 */
class BudgetPeriodApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Periodes', only: ['index']),
            new Middleware('permission:Ver Budget Periodes', only: ['show']),
            new Middleware('permission:Crear Budget Periodes', only: ['store']),
            new Middleware('permission:Editar Budget Periodes', only: ['update']),
            new Middleware('permission:Eliminar Budget Periodes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_periods.
     * GET|HEAD /budget_periods
     */
    public function index(Request $request): JsonResponse
    {
        $budget_periods = QueryBuilder::for(BudgetPeriod::class)
            ->allowedFilters([
                'user_id',
                'budget_template_id',
                'start_date',
                'end_date',
                'is_active',
                'total_budgeted',
                AllowedFilter::scope('conAtributoAdicional','conAtributoAdicional')
            ])
            ->allowedSorts([
                'user_id',
                'budget_template_id',
                'start_date',
                'end_date',
                'is_active',
                'total_budgeted'
            ])
            ->allowedIncludes([
                'budgetTemplate',
                'user'
            ])
            ->defaultSort('-id')
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_periods, 'budget_periods recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetPeriod in storage.
     * POST /budget_periods
     */
    public function store(CreateBudgetPeriodApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $budget_periods = BudgetPeriod::create($input);

        return $this->sendResponse($budget_periods->toArray(), 'BudgetPeriod creado con éxito.');
    }

    /**
     * Display the specified BudgetPeriod.
     * GET|HEAD /budget_periods/{id}
     */
    public function show(BudgetPeriod $budgetperiod)
    {
        return $this->sendResponse($budgetperiod->toArray(), 'BudgetPeriod recuperado con éxito.');
    }

    /**
     * Update the specified BudgetPeriod in storage.
     * PUT/PATCH /budget_periods/{id}
     */
    public function update(UpdateBudgetPeriodApiRequest $request, $id): JsonResponse
    {
        $budgetperiod = BudgetPeriod::findOrFail($id);
        $budgetperiod->update($request->validated());
        return $this->sendResponse($budgetperiod, 'BudgetPeriod actualizado con éxito.');
    }

    /**
     * Remove the specified BudgetPeriod from storage.
     * DELETE /budget_periods/{id}
     */
    public function destroy(BudgetPeriod $budgetperiod): JsonResponse
    {
        $budgetperiod->delete();
        return $this->sendResponse(null, 'BudgetPeriod eliminado con éxito.');
    }
}
