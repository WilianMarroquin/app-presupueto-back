<?php

namespace App\Services\Transaction\DOT;

readonly class TransactionDTO {
    public function __construct(
        public int $category_id,
        public int $account_id,
        public float $amount,
        public ?string $description = null,
        public int $payment_method_id,
        public ?int $is_recurring = 0,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            category_id: $data['category_id'],
            account_id: $data['account_id'],
            amount: $data['amount'],
            description: $data['description'] ?? null,
            payment_method_id: $data['payment_method_id'],
            is_recurring: $data['is_recurring'] ?? 0
        );
    }
    public function toArray(): array {
        return [
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'payment_method_id' => $this->payment_method_id,
            'is_recurring' => $this->is_recurring,
        ];
    }
}
