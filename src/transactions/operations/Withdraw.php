<?php

namespace Financial\Transactions\Transactions\Operations;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\BaseTransaction;
use Financial\Transactions\Transactions\TransactionCalculator;

class Withdraw extends BaseTransaction
{
    protected string $type = TransactionEnum::WITHDRAW;

    public function __construct(int $accountNumber, float $amount, string $comment, string $dueDate)
    {
        parent::__construct($accountNumber, $this->type, $amount, $comment, $dueDate);
    }

    /**
     * @throws Exception
     */
    public function handle(Account $account): void
    {
        $accountBalance = $account->getAccountBalance($this->getAccountNumber());

        if ($accountBalance < $this->getAmount()) {
            throw new Exception("Insufficient fund");
        }

        $account->createTransaction($this->getPayload());
    }
}
