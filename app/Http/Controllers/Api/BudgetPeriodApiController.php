<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetPeriodApiRequest;
use App\Http\Requests\Api\UpdateBudgetPeriodApiRequest;
use App\Models\BudgetItem;
use App\Models\BudgetPeriod;
use App\Models\Transaction;
use App\Models\TransactionCategory;
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

    /**
     * Obtiene el resumen global del período contable activo (o seleccionado)
     * junto con la lista de períodos y el estado de salud presupuestaria.
     * GET /budget_periods/summary/global
     */
    public function globalSummary(): JsonResponse
    {
        // Obtener únicamente el período contable activo actual mediante el scope
        $activePeriod = BudgetPeriod::with('budgetTemplate:id,name')
            ->onlyCurrentAccountingPeriod()
            ->first();

        if (!$activePeriod) {
            return $this->sendResponse([
                'active_period' => null,
                'health'        => [
                    'total_budgeted'  => 0,
                    'total_spent'     => 0,
                    'remaining'       => 0,
                    'percentage_used' => 0,
                    'status'          => 'success',
                ],
            ], 'No existe un período contable activo configurado.');
        }

        // 1. Techo presupuestado de categorías de tipo Gasto
        $totalBudgeted = (float) BudgetItem::where('budget_template_id', $activePeriod->budget_template_id)
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->sum('category_limit');

        if ($totalBudgeted <= 0 && $activePeriod->total_budgeted > 0) {
            $totalBudgeted = (float) $activePeriod->total_budgeted;
        }

        // 2. Rango de fechas: desde el inicio del período hasta hoy (o fin de mes si aún está en curso)
        $startDate = $activePeriod->start_date->toDateString();
        $endDate = ($activePeriod->end_date ?? now())->toDateString();

        // 3. Total gastado en el período (prioriza budget_date, fallback a transaction_date)
        $totalSpent = (float) Transaction::query()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('budget_date', [$startDate, $endDate])
                    ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->whereNull('budget_date')
                            ->whereBetween('transaction_date', [$startDate, $endDate]);
                    });
            })
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->sum('amount');

        // 4. Cálculos de salud
        $remaining = $totalBudgeted - $totalSpent;
        $percentageUsed = $totalBudgeted > 0
            ? round(($totalSpent / $totalBudgeted) * 100, 1)
            : ($totalSpent > 0 ? 100.0 : 0.0);

        $status = 'success';
        if ($percentageUsed >= 90 || ($totalBudgeted > 0 && $totalSpent > $totalBudgeted)) {
            $status = 'error';
        } elseif ($percentageUsed >= 75) {
            $status = 'warning';
        }

        return $this->sendResponse([
            'active_period' => [
                'id'                   => $activePeriod->id,
                'budget_template_id'   => $activePeriod->budget_template_id,
                'template_name'        => $activePeriod->budgetTemplate->name ?? 'Plantilla',
                'start_date'           => $activePeriod->start_date?->format('Y-m-d'),
                'end_date'             => $activePeriod->end_date?->format('Y-m-d'),
                'start_date_formatted' => $activePeriod->start_date?->format('d/m/Y'),
                'end_date_formatted'   => $activePeriod->end_date ? $activePeriod->end_date->format('d/m/Y') : 'Actualidad',
                'texto'                => trim($activePeriod->texto ?? ''),
                'is_active'            => true,
            ],
            'health' => [
                'total_budgeted'  => round($totalBudgeted, 2),
                'total_spent'     => round($totalSpent, 2),
                'remaining'       => round($remaining, 2),
                'percentage_used' => $percentageUsed,
                'status'          => $status,
            ],
        ], 'Resumen global presupuestario recuperado con éxito.');
    }
}
