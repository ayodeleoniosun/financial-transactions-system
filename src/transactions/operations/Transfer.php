<?php

namespace Financial\Transactions\Transactions\Operations;

use Exception;
use Financial\Transactions\Accounts\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\BaseTransaction;

class Transfer extends BaseTransaction
{
    protected string $type = TransactionEnum::TRANSFER;

    public function __construct(int $accountNumber, float $amount, string $comment, string $dueDate, int $recipient)
    {
        parent::__construct($accountNumber, $this->type, $amount, $comment, $dueDate, $recipient);
    }

    /**
     * @throws Exception
     */
    public function handle(Account $account): void
    {
        $accountBalance = $account->getAccountBalance();

        if ($accountBalance < $this->getAmount()) {
            throw new Exception("Insufficient fund");
        }

        $account->createTransaction($this->getPayload());
    }
}
