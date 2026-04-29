<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetTemplateApiRequest;
use App\Http\Requests\Api\UpdateBudgetTemplateApiRequest;
use App\Models\BudgetTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $input = $request->all();

        $budget_templates = BudgetTemplate::create($input);

        return $this->sendResponse($budget_templates->toArray(), 'BudgetTemplate creado con éxito.');
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
}
