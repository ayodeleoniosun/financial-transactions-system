<?php

namespace Financial\Transactions\Transactions;

use Financial\Transactions\Enums\FilterTransactionEnum;

class FilterTransactions
{
    private array $transactions;

    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    public function filterTransactionsByDueDate($filterType = FilterTransactionEnum::ASCENDING): array
    {
        usort($this->transactions, function ($a, $b) use ($filterType) {
            if ($filterType === FilterTransactionEnum::ASCENDING) {
                return strcmp($a->dueDate, $b->dueDate);
            }

            return strcmp($b->dueDate, $a->dueDate);
        });

        return $this->transactions;
    }

    public function filterTransactionsByComment($filterType = FilterTransactionEnum::ASCENDING): array
    {
        usort($this->transactions, function ($a, $b) use ($filterType) {
            if ($filterType === FilterTransactionEnum::ASCENDING) {
                return strcmp($a->comment, $b->comment);
            }

            return strcmp($b->comment, $a->comment);
        });

        return $this->transactions;
    }
}