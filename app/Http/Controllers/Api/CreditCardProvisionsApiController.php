<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateCreditCardProvisionsApiRequest;
use App\Http\Requests\Api\UpdateCreditCardProvisionsApiRequest;
use App\Models\CreditCardProvisions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CreditCardProvisionsApiController
 */
class CreditCardProvisionsApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Credit Card Provisionses', only: ['index']),
            new Middleware('permission:Ver Credit Card Provisionses', only: ['show']),
            new Middleware('permission:Crear Credit Card Provisionses', only: ['store']),
            new Middleware('permission:Editar Credit Card Provisionses', only: ['update']),
            new Middleware('permission:Eliminar Credit Card Provisionses', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Credit_card_provisions.
     * GET|HEAD /credit_card_provisions
     */
    public function index(Request $request): JsonResponse
    {
        $credit_card_provisions = QueryBuilder::for(CreditCardProvisions::class)
            ->allowedFilters([
    'installment_plan_id',
    'installment_number',
    'month',
    'year',
    'amount',
    'status',
    'transaction_id'
])
            ->allowedSorts([
    'installment_plan_id',
    'installment_number',
    'month',
    'year',
    'amount',
    'status',
    'transaction_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($credit_card_provisions, 'credit_card_provisions recuperados con éxito.');
    }


    /**
     * Store a newly created CreditCardProvisions in storage.
     * POST /credit_card_provisions
     */
    public function store(CreateCreditCardProvisionsApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $credit_card_provisions = CreditCardProvisions::create($input);

        return $this->sendResponse($credit_card_provisions->toArray(), 'CreditCardProvisions creado con éxito.');
    }

    /**
     * Display the specified CreditCardProvisions.
     * GET|HEAD /credit_card_provisions/{id}
     */
    public function show(CreditCardProvisions $creditcardprovisions)
    {
        return $this->sendResponse($creditcardprovisions->toArray(), 'CreditCardProvisions recuperado con éxito.');
    }

    /**
    * Update the specified CreditCardProvisions in storage.
    * PUT/PATCH /credit_card_provisions/{id}
    */
    public function update(UpdateCreditCardProvisionsApiRequest $request, $id): JsonResponse
    {
        $creditcardprovisions = CreditCardProvisions::findOrFail($id);
        $creditcardprovisions->update($request->validated());
        return $this->sendResponse($creditcardprovisions, 'CreditCardProvisions actualizado con éxito.');
    }

    /**
    * Remove the specified CreditCardProvisions from storage.
    * DELETE /credit_card_provisions/{id}
    */
    public function destroy(CreditCardProvisions $creditcardprovisions): JsonResponse
    {
        $creditcardprovisions->delete();
        return $this->sendResponse(null, 'CreditCardProvisions eliminado con éxito.');
    }
}
