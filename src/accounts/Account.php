<?php

namespace Financial\Transactions\Accounts;

use Financial\Transactions\Enums\TransactionEnum;

class Account
{
    private array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function createTransaction($payload): void
    {
        $this->transactions[] = (object)$payload;
    }

    public function getAccountBalance(int $accountNumber): float|int
    {
        $deposits = array_sum(array_column($this->getDepositTransactions($accountNumber), 'amount'));
        $withdrawals = array_sum(array_column($this->getWithdrawalTransactions($accountNumber), 'amount'));
        $transfers = array_sum(array_column($this->getTransferTransactions($accountNumber), 'amount'));

        return $deposits - ($withdrawals + $transfers);
    }

    public function getDepositTransactions(int $accountNumber): array
    {
        return $this->getTransactionsByType($accountNumber, TransactionEnum::DEPOSIT);
    }

    public function getTransactionsByType(int $accountNumber, string $type): array
    {
        return array_values(array_filter($this->transactions, function ($transaction) use ($accountNumber, $type) {
            return $transaction->accountNumber === $accountNumber && $transaction->type === $type;
        }));
    }

    public function getWithdrawalTransactions(int $accountNumber): array
    {
        return $this->getTransactionsByType($accountNumber, TransactionEnum::WITHDRAW);
    }

    public function getTransferTransactions(int $accountNumber): array
    {
        return $this->getTransactionsByType($accountNumber, TransactionEnum::TRANSFER);
    }

    public function getAccountTransactions(int $accountNumber): array
    {
        return array_values(array_filter($this->transactions, function ($transaction) use ($accountNumber) {
            return $transaction->accountNumber === $accountNumber;
        }));
    }

}