<?php

namespace Financial\Transactions;

class BaseTransaction
{
    protected array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function createTransaction($transaction): void
    {
        $this->transactions[$transaction->id] = $transaction;
    }
}
