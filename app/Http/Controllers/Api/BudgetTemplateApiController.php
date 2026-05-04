<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetPeriod;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetTemplateApiRequest;
use App\Http\Requests\Api\UpdateBudgetTemplateApiRequest;
use App\Models\BudgetTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetTemplateApiController
 */
class BudgetTemplateApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Templates', only: ['index']),
            new Middleware('permission:Ver Budget Templates', only: ['show']),
            new Middleware('permission:Crear Budget Templates', only: ['store']),
            new Middleware('permission:Editar Budget Templates', only: ['update']),
            new Middleware('permission:Eliminar Budget Templates', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_templates.
     * GET|HEAD /budget_templates
     */
    public function index(Request $request): JsonResponse
    {
        $budget_templates = QueryBuilder::for(BudgetTemplate::class)
            ->allowedFilters([
                'user_id',
                'name',
                'description',
                'period_type',
                'total_estimated_amount'
            ])
            ->allowedSorts([
                'user_id',
                'name',
                'description',
                'period_type',
                'total_estimated_amount'
            ])
            ->allowedIncludes([
                'periodType'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_templates, 'budget_templates recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetTemplate in storage.
     * POST /budget_templates
     */
    public function store(CreateBudgetTemplateApiRequest $request): JsonResponse
    {

        $request->merge([
            'user_id' => auth()->id(),
        ]);

        $input = $request->all();

        $budget_templates = BudgetTemplate::create($input);

        return $this->sendResponse($budget_templates->toArray(), 'Budget Template created successfully');
    }

    /**
     * Display the specified BudgetTemplate.
     * GET|HEAD /budget_templates/{id}
     */
    public function show(BudgetTemplate $budgettemplate)
    {
        return $this->sendResponse($budgettemplate->toArray(), 'BudgetTemplate recuperado con éxito.');
    }

    /**
     * Update the specified BudgetTemplate in storage.
     * PUT/PATCH /budget_templates/{id}
     */
    public function update(UpdateBudgetTemplateApiRequest $request, $id): JsonResponse
    {
        $budgettemplate = BudgetTemplate::findOrFail($id);
        $budgettemplate->update($request->validated());
        return $this->sendResponse($budgettemplate, 'BudgetTemplate actualizado con éxito.');
    }

    /**
     * Remove the specified BudgetTemplate from storage.
     * DELETE /budget_templates/{id}
     */
    public function destroy(BudgetTemplate $budgettemplate): JsonResponse
    {
        $budgettemplate->delete();
        return $this->sendResponse(null, 'BudgetTemplate eliminado con éxito.');
    }

    public function activated(BudgetTemplate $budget_template)
    {
        $user = auth()->user();

        return DB::transaction(function () use ($user, $budget_template) {

            /** @var BudgetPeriod $ultimaPlantillaActiva */
            $ultimaPlantillaActiva = $user->latestActiveBudgetTemplate;

            // Definimos el inicio de este mes (ej. 1 de mayo)
            $inicioMesActual = today()->startOfMonth();

            if ($ultimaPlantillaActiva) {
                $fechaInicioVieja = Carbon::parse($ultimaPlantillaActiva->start_date);

                // MANEJO DEL EDGE CASE: ¿La plantilla vieja inició este mismo mes?
                if ($fechaInicioVieja->gte($inicioMesActual)) {
                    // Nunca vivió un mes completo. La eliminamos para mantener limpio el historial.
                    // Nota: Usar forceDelete() si usas SoftDeletes, para borrar el rastro.
                    $ultimaPlantillaActiva->delete();
                } else {
                    // CAMINO NORMAL (Cierre Histórico):
                    // Cortamos el mes pasado exacto (ej. 30 de abril)
                    $ultimaPlantillaActiva->update([
                        'is_active' => false,
                        'end_date'  => today()->subMonth()->endOfMonth(),
                    ]);
                }
            }

            // ACTIVACIÓN RETROACTIVA:
            // La nueva plantilla abarca todo el mes actual desde el día 1
            $user->budgetPeriods()->create([
                'budget_template_id' => $budget_template->id,
                'start_date'         => $inicioMesActual,
                'end_date'           => null, // Periodo abierto (Perpetual Budget)
                'is_active'          => true,
                'total_budgeted'     => $budget_template->total_estimated_amount ?? 0,
            ]);

            return $this->sendSuccess('Presupuesto activado con éxito.');
        });
    }


    public function getActiveBudgetLimits(): JsonResponse
    {
        $user = auth()->user();

        /** @var BudgetPeriod $activePeriod */
        $activePeriod = $user->latestActiveBudgetTemplate;

        if (!$activePeriod) {
            return $this->sendResponse([], 'No hay presupuesto activo.');
        }

        $budgetItems = $activePeriod->budgetTemplate->items()
            ->with('category:id,name') // Cargamos la categoría para tener el nombre
            ->get()
            ->map(function ($item) {
                return [
                    'category_id'   => $item->transaction_category_id,
                    'category_name' => $item->category->name ?? 'Sin nombre',
                    // Lo mandamos como 'amount' porque así lo suma tu computed en Vue
                    'amount'        => (float) $item->category_limit,
                ];
            });

        return $this->sendResponse($budgetItems, 'Límites recuperados con éxito.');
    }

}
