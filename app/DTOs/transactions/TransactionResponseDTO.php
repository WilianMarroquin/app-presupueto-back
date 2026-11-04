<?php

namespace App\DTOs\transactions;

use App\Models\Transaction;

readonly class TransactionResponseDTO
{
    public function __construct(
        public int $id,
        public int $categoryId,
        public int $accountId,
        public float $amount,
        public string $description,
        public string $transactionDate,
        public ?string $budgetDate,
        public bool $isSettled,
        public float $settledAmount,
    ) {}

    /**
     * Factory method para crear el DTO desde el modelo.
     */
    public static function fromModel(Transaction $transaction): self
    {
        return new self(
            id: $transaction->id,
            categoryId: $transaction->category_id,
            accountId: $transaction->account_id,
            amount: (float) $transaction->amount,
            description: $transaction->description,
            transactionDate: $transaction->transaction_date->toDateTimeString(),
            budgetDate: $transaction->budget_date ? $transaction->budget_date->toDateString() : null,
            isSettled: (bool) $transaction->is_settled,
            settledAmount: (float) $transaction->settled_amount,
        );
    }

    /**
     * Convertir a array para la respuesta JSON.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'account_id' => $this->accountId,
            'amount' => $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->transactionDate,
            'budget_date' => $this->budgetDate,
            'is_settled' => $this->isSettled,
            'settled_amount' => $this->settledAmount,
        ];
    }
}
