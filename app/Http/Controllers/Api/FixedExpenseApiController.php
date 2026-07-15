<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateFixedExpenseApiRequest;
use App\Http\Requests\Api\UpdateFixedExpenseApiRequest;
use App\Models\FixedExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class FixedExpenseApiController
 */
class FixedExpenseApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Fixed Expenses', only: ['index']),
            new Middleware('permission:Ver Fixed Expenses', only: ['show']),
            new Middleware('permission:Crear Fixed Expenses', only: ['store']),
            new Middleware('permission:Editar Fixed Expenses', only: ['update']),
            new Middleware('permission:Eliminar Fixed Expenses', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Fixed_expenses.
     * GET|HEAD /fixed_expenses
     */
    public function index(Request $request): JsonResponse
    {
        $fixed_expenses = QueryBuilder::for(FixedExpense::class)
            ->allowedFilters([
                'transaction_category_id',
                'name',
                'description',
                'base_amount',
                'due_day',
                'is_variable',
                'is_active',
                AllowedFilter::scope('conAtributoAdicional','conAtributoAdicional')
            ])
            ->allowedSorts([
                'transaction_category_id',
                'name',
                'description',
                'base_amount',
                'due_day',
                'is_variable',
                'is_active'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($fixed_expenses, 'fixed_expenses recuperados con éxito.');
    }


    /**
     * Store a newly created FixedExpense in storage.
     * POST /fixed_expenses
     */
    public function store(CreateFixedExpenseApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $fixed_expenses = FixedExpense::create($input);

        return $this->sendResponse($fixed_expenses->toArray(), 'FixedExpense creado con éxito.');
    }

    /**
     * Display the specified FixedExpense.
     * GET|HEAD /fixed_expenses/{id}
     */
    public function show(FixedExpense $fixed_expense)
    {
        return $this->sendResponse($fixed_expense->toArray(), 'FixedExpense recuperado con éxito.');
    }

    /**
     * Update the specified FixedExpense in storage.
     * PUT/PATCH /fixed_expenses/{id}
     */
    public function update(UpdateFixedExpenseApiRequest $request, $id): JsonResponse
    {
        $fixedexpense = FixedExpense::findOrFail($id);
        $fixedexpense->update($request->validated());
        return $this->sendResponse($fixedexpense, 'FixedExpense actualizado con éxito.');
    }

    /**
     * Remove the specified FixedExpense from storage.
     * DELETE /fixed_expenses/{id}
     */
    public function destroy(FixedExpense $fixed_expense): JsonResponse
    {
        $fixed_expense->delete();
        return $this->sendResponse(null, 'FixedExpense eliminado con éxito.');
    }

    public function pay(Request $request): JsonResponse
    {
        $request->validate([
            'fixed_expense_id' => 'required',
            'amount' => 'nullable|numeric',
            'account_id' => 'nullable|integer',
        ]);

        $actuallyPeriod = currentAccountingPeriod();
        $fixed_expense = FixedExpense::findOrFail($request->fixed_expense_id);
        $accountId = $request->account_id;
        $amount = $request->amount;

        if (!$accountId){
            $accountId = usuarioAutenticado()->accountTransactional->id;
        }

        if(!$amount) {
            $amount = $fixed_expense->base_amount;
        }

        $datos = [
            'account_id' => $accountId,
            'amount' => $amount,
            'description' => $fixed_expense->description,
            'payment_method_id' => $input['payment_method_id'],
            'category_id' => $input['category_id'],
        ];
        $dpo = TransactionDTO::fromArray($datos);

        $respuesta = $createTransactionService->execute($dpo);

        $fixed_expense->paidPeriods()
            ->create([
            'budget_period_id' => $actuallyPeriod->id,
            'transaction_id' => null,
            'fixed_expense_id' => $fixed_expense->id,
        ]);

    }
}
