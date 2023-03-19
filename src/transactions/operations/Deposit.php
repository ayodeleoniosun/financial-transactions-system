<?php

namespace Financial\Transactions\Transactions\Operations;

use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\Transactions\BaseTransaction;
use Financial\Transactions\Transactions\TransactionCalculator;

class Deposit extends BaseTransaction
{
    protected string $type = TransactionEnum::DEPOSIT;

    public function __construct(int $accountNumber, float $amount, string $comment, string $dueDate)
    {
        parent::__construct($accountNumber, $this->type, $amount, $comment, $dueDate);
    }

    public function handle(TransactionCalculator $transactionCalculator): void
    {
        $transactionCalculator->addTransaction(
            $this->getAccountNumber(),
            $this->getType(),
            $this->getAmount(),
            $this->getComment(),
            $this->getDueDate()
        );
    }
}
