<?php

namespace Financial\Transactions\Transactions\Operations;

use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\BaseTransaction;

class Deposit extends BaseTransaction
{
    protected string $type = TransactionEnum::DEPOSIT;

    public function __construct(int $accountNumber, float $amount, string $comment, string $dueDate)
    {
        parent::__construct($accountNumber, $this->type, $amount, $comment, $dueDate);
    }

    public function handle(Account $account): void
    {
        $account->createTransaction($this->getPayload());
    }
}
