<?php

namespace Financial\Transactions\interfaces;

use Financial\Transactions\Account;

interface TransactionServiceInterface
{
    public static function handler(float $amount, Account $sender, Account|null $recipient = null): object;

    public static function transactionType(): string;
}