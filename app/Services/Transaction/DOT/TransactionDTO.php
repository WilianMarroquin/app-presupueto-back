<?php

namespace App\Services\Transaction\DOT;

readonly class TransactionDTO {
    public function __construct(
        public int $account_id,
        public float $amount,
        public ?string $description = null,
        public int $payment_method_id,
        public ?int $category_id = null,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            account_id: $data['account_id'],
            amount: $data['amount'],
            description: $data['description'] ?? null,
            payment_method_id: $data['payment_method_id'],
            category_id: $data['category_id'] ?? null,
        );
    }
    public function toArray(): array {
        return [
            'account_id' => $this->account_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'payment_method_id' => $this->payment_method_id,
            'category_id' => $this->category_id,
        ];
    }
}
