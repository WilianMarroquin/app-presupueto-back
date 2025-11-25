<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{

    public function createTransaction(TransactionDTO $data): array
    {
        try {
            DB::beginTransaction();

            $payload = $data->toArray();

            $payload['transaction_date'] = now();

            $transaction = Transaction::create($payload);

            if($transaction->category->isExpense()){
                $transaction->account->debit($transaction->amount);
            }
            if($transaction->category->isIncome()){
                $transaction->account->accredit($transaction->amount);
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error al crear la transacción: ' . $e->getMessage(),
            ];
        }
        return [
            'success' => true,
            'transaction' => $transaction->toArray(),
            'message' => 'Transacción creada con éxito.',
        ];

    }

}
