<?php

namespace Financial\Transactions\Transactions;

abstract class Transaction
{
    protected string $type;
    protected float $amount;
    protected string $comment;
    protected string $dueDate;
    protected int $accountNumber;

    /**
     * @param string $type
     * @param float $amount
     * @param string $comment
     * @param string $dueDate
     * @param int $accountNumber
     */
    public function __construct(int $accountNumber, string $type, float $amount, string $comment, string $dueDate)
    {
        $this->accountNumber = $accountNumber;
        $this->type = $type;
        $this->amount = $amount;
        $this->comment = $comment;
        $this->dueDate = $dueDate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function getAccountNumber(): int
    {
        return $this->accountNumber;
    }

    abstract public function handle(TransactionCalculator $transactionCalculator);
}
