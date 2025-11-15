<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAccountTypeApiRequest;
use App\Http\Requests\Api\UpdateAccountTypeApiRequest;
use App\Models\AccountType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AccountTypeApiController
 */
class AccountTypeApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Account Types', only: ['index']),
            new Middleware('permission:Ver Account Types', only: ['show']),
            new Middleware('permission:Crear Account Types', only: ['store']),
            new Middleware('permission:Editar Account Types', only: ['update']),
            new Middleware('permission:Eliminar Account Types', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Account_types.
     * GET|HEAD /account_types
     */
    public function index(Request $request): JsonResponse
    {
        $account_types = QueryBuilder::for(AccountType::class)
            ->allowedFilters([
    'name'
])
            ->allowedSorts([
    'name'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($account_types, 'account_types recuperados con éxito.');
    }


    /**
     * Store a newly created AccountType in storage.
     * POST /account_types
     */
    public function store(CreateAccountTypeApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $account_types = AccountType::create($input);

        return $this->sendResponse($account_types->toArray(), 'AccountType creado con éxito.');
    }

    /**
     * Display the specified AccountType.
     * GET|HEAD /account_types/{id}
     */
    public function show(AccountType $accounttype)
    {
        return $this->sendResponse($accounttype->toArray(), 'AccountType recuperado con éxito.');
    }

    /**
    * Update the specified AccountType in storage.
    * PUT/PATCH /account_types/{id}
    */
    public function update(UpdateAccountTypeApiRequest $request, $id): JsonResponse
    {
        $accounttype = AccountType::findOrFail($id);
        $accounttype->update($request->validated());
        return $this->sendResponse($accounttype, 'AccountType actualizado con éxito.');
    }

    /**
    * Remove the specified AccountType from storage.
    * DELETE /account_types/{id}
    */
    public function destroy(AccountType $accounttype): JsonResponse
    {
        $accounttype->delete();
        return $this->sendResponse(null, 'AccountType eliminado con éxito.');
    }
}
