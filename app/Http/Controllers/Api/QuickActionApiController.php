<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateQuickActionRequest;
use App\Http\Requests\Api\UpdateQuickActionRequest;
use App\Models\QuickAction;
use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class QuickActionApiController extends AppBaseController
{
    /**
     * Display a listing of QuickActions.
     * GET /api/v1/quick-actions
     */
    public function index(Request $request): JsonResponse
    {
        $quickActions = QueryBuilder::for(QuickAction::class)
            ->with(['category', 'account.currency', 'paymentMethod'])
            ->allowedFilters([
                'name',
                'category_id',
                'account_id',
                'payment_method_id',
            ])
            ->allowedSorts([
                'id',
                'name',
                'amount',
                'created_at',
            ])
            ->allowedIncludes([
                'category',
                'account',
                'paymentMethod',
            ])
            ->defaultSort('-id')
            ->jsonPaginate($request->input('per_page', $request->input('page.size', 50)));

        return $this->sendResponse($quickActions, 'Acciones rápidas recuperadas con éxito.');
    }

    /**
     * Store a newly created QuickAction in storage.
     * POST /api/v1/quick-actions
     */
    public function store(CreateQuickActionRequest $request): JsonResponse
    {
        $quickAction = QuickAction::create($request->validated());
        $quickAction->load(['category', 'account.currency', 'paymentMethod']);

        return response()->json([
            'success' => true,
            'data' => $quickAction,
            'message' => 'Acción rápida creada con éxito.',
        ], 201);
    }

    /**
     * Display the specified QuickAction.
     * GET /api/v1/quick-actions/{id}
     */
    public function show($id): JsonResponse
    {
        $quickAction = QuickAction::with(['category', 'account.currency', 'paymentMethod'])->findOrFail($id);

        return $this->sendResponse($quickAction, 'Acción rápida recuperada con éxito.');
    }

    /**
     * Update the specified QuickAction in storage.
     * PUT/PATCH /api/v1/quick-actions/{id}
     */
    public function update(UpdateQuickActionRequest $request, $id): JsonResponse
    {
        $quickAction = QuickAction::findOrFail($id);
        $quickAction->update($request->validated());
        $quickAction->load(['category', 'account.currency', 'paymentMethod']);

        return $this->sendResponse($quickAction, 'Acción rápida actualizada con éxito.');
    }

    /**
     * Remove the specified QuickAction from storage.
     * DELETE /api/v1/quick-actions/{id}
     */
    public function destroy($id): JsonResponse
    {
        $quickAction = QuickAction::findOrFail($id);
        $quickAction->delete();

        return $this->sendResponse(null, 'Acción rápida eliminada con éxito.');
    }

    /**
     * Execute a QuickAction and register a Transaction.
     * POST /api/v1/quick-actions/{id}/execute
     */
    public function execute(Request $request, $id): JsonResponse
    {
        $quickAction = QuickAction::findOrFail($id);

        $validated = $request->validate([
            'account_id' => [
                is_null($quickAction->account_id) ? 'required' : 'nullable',
                'integer',
                'exists:accounts,id',
            ],
            'payment_method_id' => [
                is_null($quickAction->payment_method_id) ? 'required' : 'nullable',
                'integer',
                'exists:transaction_payment_methods,id',
            ],
            'amount' => [
                is_null($quickAction->amount) ? 'required' : 'nullable',
                'numeric',
                'min:0.01',
            ],
            'description' => ['nullable', 'string'],
        ]);

        $accountId = (int) ($validated['account_id'] ?? $quickAction->account_id);
        $paymentMethodId = (int) ($validated['payment_method_id'] ?? $quickAction->payment_method_id);
        $amount = (float) ($validated['amount'] ?? $quickAction->amount);
        $description = array_key_exists('description', $validated) && !is_null($validated['description'])
            ? $validated['description']
            : ($quickAction->description ?? $quickAction->name);

        try {
            $dto = TransactionDTO::fromArray([
                'account_id' => $accountId,
                'amount' => $amount,
                'description' => $description,
                'payment_method_id' => $paymentMethodId,
                'category_id' => $quickAction->category_id,
            ]);

            $createTransactionService = new CreateTransactionService();
            $respuesta = $createTransactionService->execute($dto);

            if (!$respuesta['success']) {
                return $this->sendError($respuesta['message'], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $respuesta['transaction'],
                'message' => 'Transacción registrada con éxito.',
            ], 201);
        } catch (\Throwable $th) {
            return $this->sendError('Error al ejecutar la acción rápida: ' . $th->getMessage(), 500);
        }
    }
}
