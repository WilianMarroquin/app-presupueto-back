<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateCreditCardDetailApiRequest;
use App\Http\Requests\Api\UpdateCreditCardDetailApiRequest;
use App\Models\CreditCardDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CreditCardDetailApiController
 */
class CreditCardDetailApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Credit Card Detailes', only: ['index']),
            new Middleware('permission:Ver Credit Card Detailes', only: ['show']),
            new Middleware('permission:Crear Credit Card Detailes', only: ['store']),
            new Middleware('permission:Editar Credit Card Detailes', only: ['update']),
            new Middleware('permission:Eliminar Credit Card Detailes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Credit_card_details.
     * GET|HEAD /credit_card_details
     */
    public function index(Request $request): JsonResponse
    {
        $credit_card_details = QueryBuilder::for(CreditCardDetail::class)
            ->allowedFilters([
    'account_id',
    'bank_name',
    'alias',
    'network',
    'color',
    'last_4',
    'cutoff_day',
    'payment_day'
])
            ->allowedSorts([
    'account_id',
    'bank_name',
    'alias',
    'network',
    'color',
    'last_4',
    'cutoff_day',
    'payment_day'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($credit_card_details, 'credit_card_details recuperados con éxito.');
    }


    /**
     * Store a newly created CreditCardDetail in storage.
     * POST /credit_card_details
     */
    public function store(CreateCreditCardDetailApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $credit_card_details = CreditCardDetail::create($input);

        return $this->sendResponse($credit_card_details->toArray(), 'CreditCardDetail creado con éxito.');
    }

    /**
     * Display the specified CreditCardDetail.
     * GET|HEAD /credit_card_details/{id}
     */
    public function show(CreditCardDetail $creditcarddetail)
    {
        return $this->sendResponse($creditcarddetail->toArray(), 'CreditCardDetail recuperado con éxito.');
    }

    /**
    * Update the specified CreditCardDetail in storage.
    * PUT/PATCH /credit_card_details/{id}
    */
    public function update(UpdateCreditCardDetailApiRequest $request, $id): JsonResponse
    {
        $creditcarddetail = CreditCardDetail::findOrFail($id);
        $creditcarddetail->update($request->validated());
        return $this->sendResponse($creditcarddetail, 'CreditCardDetail actualizado con éxito.');
    }

    /**
    * Remove the specified CreditCardDetail from storage.
    * DELETE /credit_card_details/{id}
    */
    public function destroy(CreditCardDetail $creditcarddetail): JsonResponse
    {
        $creditcarddetail->delete();
        return $this->sendResponse(null, 'CreditCardDetail eliminado con éxito.');
    }
}
