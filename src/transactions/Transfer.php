<?php

namespace Financial\Transactions\Transactions;

use Financial\Transactions\Enums\TransactionEnum;

class Transfer extends Transaction
{
    protected string $type = TransactionEnum::TRANSFER;

    public function __construct(int $accountNumber, float $amount, string $comment, string $dueDate)
    {
        parent::__construct($accountNumber, $this->type, $amount, $comment, $dueDate);
    }

    public function handle(TransactionCalculator $transactionCalculator): void
    {
        $transactionCalculator->addTransaction(
            $this->getType(),
            $this->getAmount(),
            $this->getComment(),
            $this->getDueDate(),
            $this->getAccountNumber()
        );
    }
}
