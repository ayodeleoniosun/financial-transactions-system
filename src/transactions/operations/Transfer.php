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

        if (empty($this->getAccountNumber()) || empty($this->getType()) || empty($this->getAmount()) || empty($this->getComment()) || empty($this->getDueDate()) || empty($this->getRecipient())) {
            throw new Exception("Please fill all fields");
        }

        if ($this->getAmount() < 1) {
            throw new Exception("The amount to be transferred must be greater than 0");
        }

        if ($account->getAccountNumber() == $this->getRecipient()) {
            throw new Exception("You cannot transfer funds to yourself");
        }

        if ($account->getAccountBalance() < $this->getAmount()) {
            throw new Exception("Insufficient fund");
        }

        $account->createTransaction($this->getPayload());
    }
}
