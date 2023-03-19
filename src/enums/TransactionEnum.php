<?php

namespace Financial\Transactions\Enums;

enum TransactionEnum: string
{
    public const DEPOSIT = 'deposit';
    public const WITHDRAW = 'withdraw';
    public const TRANSFER = 'transfer';
}
