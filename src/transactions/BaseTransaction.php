<?php

namespace Financial\Transactions\Transactions;

use Financial\Transactions\Accounts\Account;

abstract class BaseTransaction
{
    protected string $type;
    protected float $amount;
    protected string $comment;
    protected string $dueDate;
    protected int $accountNumber;
    protected int|null $recipient;

    /**
     * @param int $accountNumber
     * @param string $type
     * @param float $amount
     * @param string $comment
     * @param string $dueDate
     * @param int|null $recipient
     */
    public function __construct(int $accountNumber, string $type, float $amount, string $comment, string $dueDate, int|null $recipient = null)
    {
        $this->accountNumber = $accountNumber;
        $this->type = $type;
        $this->amount = $amount;
        $this->comment = $comment;
        $this->dueDate = $dueDate;
        $this->recipient = $recipient;
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

    public function getRecipient(): int|null
    {
        return $this->recipient;
    }

    abstract public function handle(Account $account);
}
