<?php

namespace Financial\Transactions\Transactions;

use Exception;
use Financial\Transactions\Enums\TransactionEnum;

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
    public function handle(TransactionCalculator $transactionCalculator): void
    {
        $accountBalance = $transactionCalculator->getAccountBalance($this->getAccountNumber());

        if ($accountBalance < $this->getAmount()) {
            throw new Exception("Insufficient fund");
        }

        $transactionCalculator->addTransaction(
            $this->getAccountNumber(),
            $this->getType(),
            $this->getAmount(),
            $this->getComment(),
            $this->getDueDate(),
            $this->getRecipient()
        );
    }
}
