<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateFixedExpensePeriodPaymentApiRequest;
use App\Http\Requests\Api\UpdateFixedExpensePeriodPaymentApiRequest;
use App\Models\FixedExpensePeriodPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class FixedExpensePeriodPaymentApiController
 */
class FixedExpensePeriodPaymentApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Fixed Expense Period Paymentes', only: ['index']),
            new Middleware('permission:Ver Fixed Expense Period Paymentes', only: ['show']),
            new Middleware('permission:Crear Fixed Expense Period Paymentes', only: ['store']),
            new Middleware('permission:Editar Fixed Expense Period Paymentes', only: ['update']),
            new Middleware('permission:Eliminar Fixed Expense Period Paymentes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Fixed_expenses_period_paymets.
     * GET|HEAD /fixed_expenses_period_paymets
     */
    public function index(Request $request): JsonResponse
    {
        $fixed_expenses_period_paymets = QueryBuilder::for(FixedExpensePeriodPayment::class)
            ->allowedFilters([
    'budget_period_id',
    'transaction_id',
    'fixed_expense_id'
])
            ->allowedSorts([
    'budget_period_id',
    'transaction_id',
    'fixed_expense_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($fixed_expenses_period_paymets, 'fixed_expenses_period_paymets recuperados con éxito.');
    }


    /**
     * Store a newly created FixedExpensePeriodPayment in storage.
     * POST /fixed_expenses_period_paymets
     */
    public function store(CreateFixedExpensePeriodPaymentApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $fixed_expenses_period_paymets = FixedExpensePeriodPayment::create($input);

        return $this->sendResponse($fixed_expenses_period_paymets->toArray(), 'FixedExpensePeriodPayment creado con éxito.');
    }

    /**
     * Display the specified FixedExpensePeriodPayment.
     * GET|HEAD /fixed_expenses_period_paymets/{id}
     */
    public function show(FixedExpensePeriodPayment $fixedexpenseperiodpayment)
    {
        return $this->sendResponse($fixedexpenseperiodpayment->toArray(), 'FixedExpensePeriodPayment recuperado con éxito.');
    }

    /**
    * Update the specified FixedExpensePeriodPayment in storage.
    * PUT/PATCH /fixed_expenses_period_paymets/{id}
    */
    public function update(UpdateFixedExpensePeriodPaymentApiRequest $request, $id): JsonResponse
    {
        $fixedexpenseperiodpayment = FixedExpensePeriodPayment::findOrFail($id);
        $fixedexpenseperiodpayment->update($request->validated());
        return $this->sendResponse($fixedexpenseperiodpayment, 'FixedExpensePeriodPayment actualizado con éxito.');
    }

    /**
    * Remove the specified FixedExpensePeriodPayment from storage.
    * DELETE /fixed_expenses_period_paymets/{id}
    */
    public function destroy(FixedExpensePeriodPayment $fixedexpenseperiodpayment): JsonResponse
    {
        $fixedexpenseperiodpayment->delete();
        return $this->sendResponse(null, 'FixedExpensePeriodPayment eliminado con éxito.');
    }
}
