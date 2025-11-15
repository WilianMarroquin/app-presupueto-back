<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAccountCurrencyApiRequest;
use App\Http\Requests\Api\UpdateAccountCurrencyApiRequest;
use App\Models\AccountCurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AccountCurrencyApiController
 */
class AccountCurrencyApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Account Currencyes', only: ['index']),
            new Middleware('permission:Ver Account Currencyes', only: ['show']),
            new Middleware('permission:Crear Account Currencyes', only: ['store']),
            new Middleware('permission:Editar Account Currencyes', only: ['update']),
            new Middleware('permission:Eliminar Account Currencyes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Account_currencys.
     * GET|HEAD /account_currencys
     */
    public function index(Request $request): JsonResponse
    {
        $account_currencys = QueryBuilder::for(AccountCurrency::class)
            ->allowedFilters([
    'name'
])
            ->allowedSorts([
    'name'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($account_currencys, 'account_currencys recuperados con éxito.');
    }


    /**
     * Store a newly created AccountCurrency in storage.
     * POST /account_currencys
     */
    public function store(CreateAccountCurrencyApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $account_currencys = AccountCurrency::create($input);

        return $this->sendResponse($account_currencys->toArray(), 'AccountCurrency creado con éxito.');
    }

    /**
     * Display the specified AccountCurrency.
     * GET|HEAD /account_currencys/{id}
     */
    public function show(AccountCurrency $accountcurrency)
    {
        return $this->sendResponse($accountcurrency->toArray(), 'AccountCurrency recuperado con éxito.');
    }

    /**
    * Update the specified AccountCurrency in storage.
    * PUT/PATCH /account_currencys/{id}
    */
    public function update(UpdateAccountCurrencyApiRequest $request, $id): JsonResponse
    {
        $accountcurrency = AccountCurrency::findOrFail($id);
        $accountcurrency->update($request->validated());
        return $this->sendResponse($accountcurrency, 'AccountCurrency actualizado con éxito.');
    }

    /**
    * Remove the specified AccountCurrency from storage.
    * DELETE /account_currencys/{id}
    */
    public function destroy(AccountCurrency $accountcurrency): JsonResponse
    {
        $accountcurrency->delete();
        return $this->sendResponse(null, 'AccountCurrency eliminado con éxito.');
    }
}
