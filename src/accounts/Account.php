<?php

namespace Financial\Transactions\Accounts;

use Financial\Transactions\Enums\TransactionEnum;

class Account
{
    private array $transactions;

    private int $accountNumber;

    public function __construct(int $accountNumber)
    {
        $this->transactions = [];
        $this->accountNumber = $accountNumber;
    }

    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    public function createTransaction($payload): object
    {
        $transactionId = count($this->transactions) + 1;

        $payload['id'] = $transactionId;

        $this->transactions[] = (object)$payload;

        $transaction = $this->getTransactionByIndex('id', $transactionId);

        return $this->transactions[$transaction];
    }

    public function getTransactionByIndex(string $column, string $value): bool|int
    {
        return array_search($value, array_column($this->getTransactions(), $column));
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getAccountBalance(): float|int
    {
        $deposits = array_sum(array_column($this->getDepositTransactions(), 'amount'));

        $withdrawals = array_sum(array_column($this->getWithdrawalTransactions(), 'amount'));

        $transfers = array_sum(array_column($this->getTransferTransactions(), 'amount'));

        return $deposits - ($withdrawals + $transfers);
    }

    public function getDepositTransactions(): array
    {
        return $this->getTransactionsByType(TransactionEnum::DEPOSIT);
    }

    public function getTransactionsByType(string $type): array
    {
        return array_values(array_filter($this->transactions, function ($transaction) use ($type) {
            return $transaction->accountNumber === $this->accountNumber && $transaction->type === $type;
        }));
    }

    public function getWithdrawalTransactions(): array
    {
        return $this->getTransactionsByType(TransactionEnum::WITHDRAW);
    }

    public function getTransferTransactions(): array
    {
        return $this->getTransactionsByType(TransactionEnum::TRANSFER);
    }

    public function getAccountTransactions(): array
    {
        return array_values(array_filter($this->transactions, function ($transaction) {
            return $transaction->accountNumber === $this->accountNumber;
        }));
    }
}