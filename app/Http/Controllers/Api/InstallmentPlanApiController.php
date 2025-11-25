<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateInstallmentPlanApiRequest;
use App\Http\Requests\Api\UpdateInstallmentPlanApiRequest;
use App\Models\InstallmentPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class InstallmentPlanApiController
 */
class InstallmentPlanApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Installment Planes', only: ['index']),
            new Middleware('permission:Ver Installment Planes', only: ['show']),
            new Middleware('permission:Crear Installment Planes', only: ['store']),
            new Middleware('permission:Editar Installment Planes', only: ['update']),
            new Middleware('permission:Eliminar Installment Planes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Installment_plans.
     * GET|HEAD /installment_plans
     */
    public function index(Request $request): JsonResponse
    {
        $installment_plans = QueryBuilder::for(InstallmentPlan::class)
            ->allowedFilters([
                'name',
                'total_amount',
                'total_installments',
                'interest_rate',
                'start_date',
                'status'
            ])
            ->allowedSorts([
                'name',
                'total_amount',
                'total_installments',
                'interest_rate',
                'start_date',
                'status'
            ])
            ->allowedIncludes([
                'paymentsMade'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($installment_plans, 'installment_plans recuperados con éxito.');
    }


    /**
     * Store a newly created InstallmentPlan in storage.
     * POST /installment_plans
     */
    public function store(CreateInstallmentPlanApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['status'] = InstallmentPlan::STATUS_ACTIVE;
        $input['start_date'] = now();

        $installment_plans = InstallmentPlan::create($input);

        return $this->sendResponse($installment_plans->toArray(), 'InstallmentPlan creado con éxito.');
    }

    /**
     * Display the specified InstallmentPlan.
     * GET|HEAD /installment_plans/{id}
     */
    public function show(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->load('payments');
        return $this->sendResponse($installmentPlan->toArray(), 'InstallmentPlan recuperado con éxito.');
    }

    /**
     * Update the specified InstallmentPlan in storage.
     * PUT/PATCH /installment_plans/{id}
     */
    public function update(UpdateInstallmentPlanApiRequest $request, $id): JsonResponse
    {
        $installmentplan = InstallmentPlan::findOrFail($id);
        $installmentplan->update($request->validated());
        return $this->sendResponse($installmentplan, 'InstallmentPlan actualizado con éxito.');
    }

    /**
     * Remove the specified InstallmentPlan from storage.
     * DELETE /installment_plans/{id}
     */
    public function destroy(InstallmentPlan $installmentplan): JsonResponse
    {
        $installmentplan->delete();
        return $this->sendResponse(null, 'InstallmentPlan eliminado con éxito.');
    }

    public function payFee(Request $request): JsonResponse
    {
        $request->validate([
            'installment_plan_id' => 'required|exists:installment_plans,id',
            'provision_id' => 'required|exists:credit_card_provisions,id',
        ]);


        return $this->sendResponse([], 'Pago realizado con éxito.');
    }
}
