<?php

namespace Financial\Transactions\Transactions;

class TransactionFactory
{
    public static function create(string $type, int $accountNumber, float $amount, string $comment, string $dueDate, int|null $recipient = null)
    {
        return new $type($accountNumber, $amount, $comment, $dueDate, $recipient);
    }
}