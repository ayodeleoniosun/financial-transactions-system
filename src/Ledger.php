<?php

namespace Financial\Transactions;

use Financial\Transactions\Enums\TransactionEnum;

class Ledger extends BaseTransaction
{
    public function getAccountDepositTransactions(Account $account, array $transactions): array
    {
        $accountTransactions = $this->getAccountTransactions($account, $transactions);

        return $this->getAccountTransactionsByType(TransactionEnum::DEPOSIT, $accountTransactions);
    }

    public function getAccountTransactions(Account $account, array $transactions): array
    {
        return array_values(array_filter($transactions, function ($transaction) use ($account) {
            return $transaction->sender === $account->getAccountNumber() || $transaction->recipient === $account->getAccountNumber();
        }));
    }

    public function getAccountTransactionsByType(string $type, array $transactions): array
    {
        return array_values(array_filter($transactions, function ($transaction) use ($type) {
            return $transaction->type === $type;
        }));
    }

    public function getAccountWithdrawalTransactions(Account $account, array $transactions): array
    {
        $accountTransactions = $this->getAccountTransactions($account, $transactions);

        return $this->getAccountTransactionsByType(TransactionEnum::WITHDRAW, $accountTransactions);
    }

    public function getAccountTransferTransactions(Account $account, array $transactions): array
    {
        $accountTransactions = $this->getAccountTransactions($account, $transactions);

        return $this->getAccountTransactionsByType(TransactionEnum::TRANSFER, $accountTransactions);
    }
}
