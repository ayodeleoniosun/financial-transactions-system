<?php

namespace Financial\Transactions\contracts;

use Financial\Transactions\Account;

interface TransactionServiceInterface
{
    public function handler(float $amount, Account $sender, Account|null $recipient = null): object;

    public function transactionType(): string;
}