<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\CreditCardProvisions;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateInstallmentPlanApiRequest;
use App\Http\Requests\Api\UpdateInstallmentPlanApiRequest;
use App\Models\InstallmentPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'provision_id' => 'required|exists:transactions,id',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $installmentPlan = InstallmentPlan::findOrFail($request->installment_plan_id);
        $provision = Transaction::findOrFail($request->provision_id);
        $createTransactionService = new CreateTransactionService();

        try {
            DB::beginTransaction();
            $datos = [
                'category_id' => TransactionCategory::FINANZAS,
                'account_id' => $request->account_id,
                'amount' => $installmentPlan->monthlyFee,
                'description' => 'Pago de cuota del plan de cuotas: ' . $installmentPlan->name,
                'payment_method_id' => TransactionPaymentMethod::TRANSFERENCIA,
            ];

            $dpo = TransactionDTO::fromArray($datos);
            $respuesta = $createTransactionService->execute($dpo);

            $provision->update([
                'is_settled' => 1,
            ]);

            if (!$respuesta['success']) {
                return $this->sendError($respuesta['message'], 500);
            }

            $transaction = $respuesta['transaction'];
            DB::commit();
            return $this->sendResponse($transaction, 'Transacción creada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al crear la transacción: ' . $e->getMessage(), 500);
        }
    }
}
