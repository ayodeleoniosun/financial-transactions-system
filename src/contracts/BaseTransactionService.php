<?php

namespace Financial\Transactions\contracts;

use Exception;
use Financial\Transactions\Account;

class BaseTransactionService
{
    public array $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    /**
     * @throws Exception
     */
    public function checkFundSufficiencyAndReturnBalance(float $amount, Account $account): float
    {
        $this->validateAmount($amount);

        $balance = $account->getAccountBalance();

        if ($balance < $amount) {
            throw new Exception("Insufficient fund");
        }

        return $balance;
    }

    /**
     * @throws Exception
     */
    public function validateAmount(float $amount): float
    {
        if ($amount < 1) {
            throw new Exception("Invalid amount. Try again");
        }

        return $amount;
    }

    public function generateTransactionId(): int
    {
        return count($this->getTransactions()) + 1;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function createTransaction($transaction): object
    {
        $this->transactions[$transaction->id] = $transaction;

        return $this->transactions[$transaction->id];
    }
}