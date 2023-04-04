<?php

namespace Financial\Transactions\services;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\Enums\TransactionEnum;
use Financial\Transactions\interfaces\TransactionServiceInterface;
use Financial\Transactions\TransactionManager;

class WithdrawalService extends TransactionManager implements TransactionServiceInterface
{
    /**
     * @throws Exception
     */
    public static function handler(float $amount, Account $account, Account|null $recipient = null): object
    {
        $oldBalance = self::checkFundSufficiencyAndReturnBalance($amount, $account);

        $newBalance = $oldBalance - $amount;

        $payload = (object)[
            'id' => self::generateTransactionId(),
            'comment' => "This is a withdrawal from {$account->getAccountNumber()}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => self::transactionType(),
            'sender' => null,
            'recipient' => $account->getAccountNumber(), //this is account withdrawn from
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ];

        $account->setAccountBalance($newBalance);

        return self::createTransaction($payload);
    }

    public static function transactionType(): string
    {
        return TransactionEnum::WITHDRAW;
    }
}