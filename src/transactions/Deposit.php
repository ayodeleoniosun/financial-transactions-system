<?php

namespace Financial\Transactions\Transactions;

use Financial\Transactions\Enums\TransactionEnum;

class Deposit extends Transaction
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
