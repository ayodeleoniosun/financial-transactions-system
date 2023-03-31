<?php

namespace Financial\Transactions\services;

use Exception;
use Financial\Transactions\Account;
use Financial\Transactions\contracts\BaseTransactionService;
use Financial\Transactions\contracts\TransactionServiceInterface;
use Financial\Transactions\Enums\TransactionEnum;

class DepositService extends BaseTransactionService implements TransactionServiceInterface
{
    /**
     * @throws Exception
     */
    public function handler(float $amount, Account $account, Account|null $recipient = null): object
    {
        $this->validateAmount($amount);

        $oldBalance = $account->getAccountBalance();

        $newBalance = $oldBalance + $amount;

        $payload = (object)[
            'id' => $this->generateTransactionId(),
            'comment' => "This is a deposit into {$account->getAccountNumber()}'s account",
            'amount' => $amount,
            'dueDate' => date("Y-m-d H:i:s"),
            'type' => $this->transactionType(),
            'sender' => null,
            'recipient' => $account->getAccountNumber(),
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ];

        $account->setAccountBalance($newBalance);

        return $this->createTransaction($payload);
    }

    public function transactionType(): string
    {
        return TransactionEnum::DEPOSIT;
    }
}