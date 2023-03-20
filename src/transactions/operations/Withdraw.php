<?php

namespace Financial\Transactions\Transactions\Operations;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\BaseTransaction;

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
        if (empty($this->getAccountNumber()) || empty($this->getType()) || empty($this->getAmount()) || empty($this->getComment()) || empty($this->getDueDate())) {
            throw new Exception("Please fill all fields");
        }

        if ($this->getAmount() < 1) {
            throw new Exception("The amount to be withdrawn must be greater than 0");
        }

        $accountBalance = $account->getAccountBalance();

        if ($accountBalance < $this->getAmount()) {
            throw new Exception("Insufficient fund");
        }

        $account->createTransaction($this->getPayload());
    }
}
