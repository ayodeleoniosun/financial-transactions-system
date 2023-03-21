<?php

namespace Financial\Transactions;

use Financial\Transactions\Enums\FilterTransactionEnum;
use Financial\Transactions\Enums\TransactionEnum;

class Ledger extends BaseTransaction
{
    public function getAccountDepositTransactions(array $transactions): array
    {
        return $this->getAccountTransactionsByType(TransactionEnum::DEPOSIT, $transactions);
    }

    public function getAccountTransactionsByType(string $type, array $transactions): array
    {
        return array_values(array_filter($transactions, function ($transaction) use ($type) {
            return $transaction->type === $type;
        }));
    }

    public function getAccountWithdrawalTransactions(array $transactions): array
    {
        return $this->getAccountTransactionsByType(TransactionEnum::WITHDRAW, $transactions);
    }

    public function getAccountTransferTransactions(array $transactions): array
    {
        return $this->getAccountTransactionsByType(TransactionEnum::TRANSFER, $transactions);
    }

    public function filterTransactionsByDueDate(array $transactions, $filterType = FilterTransactionEnum::ASCENDING): array
    {
        usort($transactions, function ($a, $b) use ($filterType) {
            if ($filterType === FilterTransactionEnum::ASCENDING) {
                return strcmp($a->dueDate, $b->dueDate);
            }

            return strcmp($b->dueDate, $a->dueDate);
        });

        return $transactions;
    }

    public function filterTransactionsByComment(array $transactions, $filterType = FilterTransactionEnum::ASCENDING): array
    {
        usort($transactions, function ($a, $b) use ($filterType) {
            if ($filterType === FilterTransactionEnum::ASCENDING) {
                return strcmp($a->comment, $b->comment);
            }

            return strcmp($b->comment, $a->comment);
        });

        return $transactions;
    }
}
