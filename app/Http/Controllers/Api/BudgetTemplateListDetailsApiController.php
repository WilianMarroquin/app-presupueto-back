<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\BudgetItemDetail;
use App\Models\FixedExpense;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateBudgetTemplateListDetailsApiRequest;
use App\Http\Requests\Api\UpdateBudgetTemplateListDetailsApiRequest;
use App\Models\BudgetTemplateListDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class BudgetTemplateListDetailsApiController
 */
class BudgetTemplateListDetailsApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Budget Template List Detailses', only: ['index']),
            new Middleware('permission:Ver Budget Template List Detailses', only: ['show']),
            new Middleware('permission:Crear Budget Template List Detailses', only: ['store']),
            new Middleware('permission:Editar Budget Template List Detailses', only: ['update']),
            new Middleware('permission:Eliminar Budget Template List Detailses', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Budget_tamplate_item_has_details.
     * GET|HEAD /budget_tamplate_item_has_details
     */
    public function index(Request $request): JsonResponse
    {
        $budget_tamplate_item_has_details = QueryBuilder::for(BudgetTemplateListDetails::class)
            ->allowedFilters([
                'budget_item_id',
                'model_type',
                'model_id'
            ])
            ->allowedSorts([
                'budget_item_id',
                'model_type',
                'model_id'
            ])
            ->allowedIncludes([
                'model'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($budget_tamplate_item_has_details, 'budget_tamplate_item_has_details recuperados con éxito.');
    }


    /**
     * Store a newly created BudgetTemplateListDetails in storage.
     * POST /budget_tamplate_item_has_details
     */
    public function store(CreateBudgetTemplateListDetailsApiRequest $request): JsonResponse
    {
        $input = $request->all();

        if ($input['es_gasto_fijo']) {
            $detail = BudgetTemplateListDetails::create([
                'budget_item_id' => $input['budget_item_id'],
                'model_type' => FixedExpense::class,
                'model_id' => $input['fixed_expense_id'],
            ]);
        } else {
            $detail = BudgetItemDetail::create([
                'name' => $input['name'],
                'amount' => $input['amount'],
            ]);

            $detail = BudgetTemplateListDetails::create([
                'budget_item_id' => $input['budget_item_id'],
                'model_type' => BudgetItemDetail::class,
                'model_id' => $detail->id
            ]);
        }

        return $this->sendResponse($detail->toArray(), 'BudgetTemplateListDetails creado con éxito.');
    }

    /**
     * Display the specified BudgetTemplateListDetails.
     * GET|HEAD /budget_tamplate_item_has_details/{id}
     */
    public function show(BudgetTemplateListDetails $budgettemplatelistdetails)
    {
        return $this->sendResponse($budgettemplatelistdetails->toArray(), 'BudgetTemplateListDetails recuperado con éxito.');
    }

    /**
     * Update the specified BudgetTemplateListDetails in storage.
     * PUT/PATCH /budget_tamplate_item_has_details/{id}
     */
    public function update(UpdateBudgetTemplateListDetailsApiRequest $request, $id): JsonResponse
    {
        $input = $request->all();

            $detail = BudgetTemplateListDetails::findOrFail($id);

            if ($input['es_gasto_fijo']) {
                // Si el detalle anterior era un BudgetItemDetail personalizado, eliminamos ese registro hijo
                if ($detail->model_type === BudgetItemDetail::class) {
                    $detail->model()?->delete();
                }

                // Actualizamos el registro pivote apuntando al nuevo FixedExpense
                $detail->update([
                    'budget_item_id' => $input['budget_item_id'],
                    'model_type' => FixedExpense::class,
                    'model_id' => $input['fixed_expense_id'],
                ]);
            } else {
                // Si ya era un BudgetItemDetail personalizado, actualizamos su nombre y monto
                if ($detail->model_type === BudgetItemDetail::class && $detail->model) {
                    $detail->model->update([
                        'name' => $input['name'],
                        'amount' => $input['amount'],
                    ]);

                    $detail->update([
                        'budget_item_id' => $input['budget_item_id'],
                    ]);
                } else {
                    // Si antes era un Gasto Fijo y ahora cambió a Normal, creamos un nuevo BudgetItemDetail
                    $customDetail = BudgetItemDetail::create([
                        'name' => $input['name'],
                        'amount' => $input['amount'],
                    ]);

                    $detail->update([
                        'budget_item_id' => $input['budget_item_id'],
                        'model_type' => BudgetItemDetail::class,
                        'model_id' => $customDetail->id,
                    ]);
                }
            }

            // Carga la relación 'model' para retornarla en la respuesta
            $detail->load('model');

            return $this->sendResponse($detail->toArray(), 'BudgetTemplateListDetails actualizado con éxito.');
    }

    /**
     * Remove the specified BudgetTemplateListDetails from storage.
     * DELETE /budget_tamplate_item_has_details/{id}
     */
    public function destroy(BudgetTemplateListDetails $budgettemplatelistdetails): JsonResponse
    {
        $budgettemplatelistdetails->delete();
        return $this->sendResponse(null, 'BudgetTemplateListDetails eliminado con éxito.');
    }
}
