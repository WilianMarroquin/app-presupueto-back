<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAccountApiRequest;
use App\Http\Requests\Api\UpdateAccountApiRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AccountApiController
 */
class AccountApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Accountes', only: ['index']),
            new Middleware('permission:Ver Accountes', only: ['show']),
            new Middleware('permission:Crear Accountes', only: ['store']),
            new Middleware('permission:Editar Accountes', only: ['update']),
            new Middleware('permission:Eliminar Accountes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Accounts.
     * GET|HEAD /accounts
     */
    public function index(Request $request): JsonResponse
    {
        $accounts = QueryBuilder::for(Account::class)
            ->allowedFilters([
                'name',
                'type_id',
                'currency_id',
                'initial_balance',
                'current_balance',
                'is_active',
                AllowedFilter::scope('onlyWithPermittedMovementId', 'onlyWithPermittedMovementId'),
                AllowedFilter::scope('withoutCreditCard', 'withoutCreditCard'),
            ])
            ->allowedSorts([
                'name',
                'type_id',
                'currency_id',
                'initial_balance',
                'current_balance',
                'is_active'
            ])
            ->allowedIncludes([
                'type',
                'currency',
                'creditCardDetail'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size') ?? 10);

        return $this->sendResponse($accounts, 'accounts recuperados con éxito.');
    }


    /**
     * Store a newly created Account in storage.
     * POST /accounts
     */
    public function store(CreateAccountApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['user_id'] = usuarioAutenticado()->id;
        $input['current_balance'] = $input['initial_balance'];
        $input['is_transactional'] = false;
        $input['is_active'] = true;

        $accounts = Account::create($input);

        return $this->sendResponse($accounts->toArray(), 'Account creado con éxito.');
    }

    /**
     * Display the specified Account.
     * GET|HEAD /accounts/{id}
     */
    public function show(Account $account)
    {
        $account->load([
            'transactionsPending.category',
            'creditCardDetail',
            'currency',
        ]);

        return $this->sendResponse($account->toArray(), 'Account recuperado con éxito.');
    }

    /**
     * Update the specified Account in storage.
     * PUT/PATCH /accounts/{id}
     */
    public function update(UpdateAccountApiRequest $request, $id): JsonResponse
    {
        if($request->is_transactional) {
            $accountOfUser = Account::whereUserId($request->user_id)->get();

            foreach ($accountOfUser as $account) {
                $account->update([
                    'is_transactional' => false
                ]);
            }
        }

        $account = Account::findOrFail($id);
        $account->update($request->validated());
        return $this->sendResponse($account, 'Account actualizado con éxito.');
    }

    /**
     * Remove the specified Account from storage.
     * DELETE /accounts/{id}
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();
        return $this->sendResponse(null, 'Account eliminado con éxito.');
    }
}
